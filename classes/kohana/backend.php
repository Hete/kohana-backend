<?php

abstract class Kohana_Backend {

    protected static $_instances = array();

    /**
     * 
     * @param type $name
     * @return Kohana_Backend
     */
    public static function instance($name = "default") {
        return array_key_exists($name, Backend::$_instances) ? Backend::$_instances[$name] : Backend::$_instances[$name] = new Backend();
    }

    private $_config;
    private $_semaphore_id;
    private $_units = array();
    private $_threads = array();

    private function __construct() {
        $this->_semaphore_id = sem_get(59736904703);
        $this->_config = Kohana::$config->load('backend.default');
    }

    public function load_units() {
        foreach ($this->_config["units"] as $unit) {
            $this->register_unit(new $unit);
        }
    }

    public function register_unit(Unit $unit) {
        $this->_units[sha1($unit->name())] = $unit;
    }

    /**
     * Run a single unit
     */
    public function execute($unit_name) {

        $this->_units[sha1($unit_name)]->run();
    }

    public function is_running() {
        $running = false;
        foreach ($this->_threads as $index => $thread) {
            if ($thread->isAlive()) {
                return true;
            } else {
                unset($this->_threads[$index]);
            }
        }
        return false;
    }

    public function start() {
        if ($this->is_running())
            throw new Kohana_Exception('Backend is already running.');

        sem_acquire($this->_semaphore_id);

        $index = 0;

        foreach ($this->_units as $unit) {

            $unit = $unit->name();

            function execute_unit($name) {

                Backend::instance()->execute($name);
            }

            if (Thread::available()) {

                $this->_threads[$index] = new Thread('execute_unit');
                $this->_threads[$index]->start($unit);
            } else {
                execute_unit($unit);
            }
        }
        sem_release($this->_semaphore_id);
    }

}

?>
