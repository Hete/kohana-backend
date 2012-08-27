<?php

class Kohana_Backend implements Unit {

    protected static $_instances = array();

    /**
     * 
     * @param type $name
     * @return Kohana_Backend
     */
    public static function instance($name = "default") {
        return array_key_exists($name, Backend::$_instances) ? Backend::$_instances[$name] : Backend::$_instances[$name] = new Backend($name);
    }

    private $_config;
    private $_name;
    private $_semaphore_id;
    private $_units = array();
    private $_threads = array();

    private function __construct($name) {

        // One semaphore by backend's instance name. So multiple backend from $_instances.

        $this->_name = $name;
        $this->_semaphore_id = sem_get(hexdec(sha1($this->_name)));

        $this->_config = Kohana::$config->load('backend.default');
    }

    public function name() {
        return $this->_name;
    }

    public function interval() {
        throw new Kohana_Exception("Calling interval on a backend has no meaning.");
    }

    ////////////////////////////////////////////////////////////////////////////
    // Work on single unit

    /**
     * Enregistre une unité. Cette unité sera executé à l'appel de run().
     * @param Unit $unit
     */
    public function register_unit(Unit $unit) {
        $this->_units[sha1($unit->name())] = $unit;
    }

    /**
     * Run a single unit
     */
    public function execute($unit_name) {

        $this->_units[sha1($unit_name)]->run();
    }

    /**
     * Retire les threads mort.
     * @param int $index est l'index du thread.
     * @throws Kohana_Exception si le thread est actif.
     */
    private function remove_dead_thread($index) {
        if (!$this->_threads[$index]->isAlive()) {
            unset($this->_threads[$index]);
        } else {
            throw new Kohana_Exception("Trying to remove alive thread at index :index", array(":index" => $index));
        }
    }

    /**
     * Détermine si le Backend roule.
     * @return boolean
     */
    public function is_running() {
        foreach ($this->_threads as $index => $thread) {
            if ($thread->isAlive()) {
                return true;
            } else {
                $this->remove_dead_thread($index);
            }
        }
        return false;
    }

    ////////////////////////////////////////////////////////////////////////////
    // Work on all units

    public function register_all_units() {
        foreach ($this->_config["units"] as $unit) {
            $this->register_unit(new $unit);
        }
    }

    public function run() {
        if ($this->is_running())
            throw new Kohana_Exception('Backend is already running.');

        sem_acquire($this->_semaphore_id);

        $shared_memory_id = shm_attach(hexdec(sha1($this->_name)));

        shm_put_var($shared_memory_id, hexdec(sha1("backend.running")), TRUE);

        $index = 0;
        foreach ($this->_units as $unit) {

            /**
             * Execute l'unité avec l'interval donné.
             * @param type $unit
             */
            function execute_unit($unit, $backend) {
                $shared_memory_id = shm_attach(hexdec(sha1($backend->name())));
                while (shm_get_var($shared_memory_id, hexdec(sha1("backend.running")))) {
                    Backend::instance()->execute($unit->name());
                    sleep($unit->interval());
                }
            }

            if (Thread::available()) {

                $this->_threads[$index] = new Thread('execute_unit');
                $this->_threads[$index]->start($unit, $this);
            } else {
                execute_unit($unit, $this);
            }

            $index++;
        }

        // Attend que les threads terminent leurs exécutions.
        $this->wait();

        shm_detach($shared_memory_id);
        sem_release($this->_semaphore_id);
    }

    // Wait until all threads (active units) die
    public function wait($interval = 1) {
        foreach ($this->_threads as $index => $thread) {
            if (!$thread->isAlive()) {

                $this->remove_dead_thread($index);
            }
            wait($interval);
        }
    }

    public function stop() {
        if (!$this->is_running())
            throw new Kohana_Exception('Backend is not running.');

        // Si ça run, on peut récupérer la portion de shm
        $shared_memory_id = shm_attach(hexdec(sha1($this->_name)));

        // On avertit le backend qu'il ne doit plus rouler.
        shm_put_var($shared_memory_id, hexdec(sha1("backend.running")), FALSE);

        // On attend que les threads crèvent.
        $this->wait();
    }

}

?>
