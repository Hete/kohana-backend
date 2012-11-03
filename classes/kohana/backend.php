<?php

abstract class Kohana_Backend {

    protected static $_instance;

    /**
     * 
     * @param type $name
     * @return Kohana_Backend
     */
    public static function instance() {
        return Backend::$_instance !== NULL ? Backend::$_instance : Backend::$_instance = new Backend();
    }

    private $_config;
    private $_semaphore_id;
    private $_units = array();

    private function __construct() {
        $this->_semaphore_id = sem_get(59736904703);
        $this->_config = Kohana::$config->load('backend');
    }

    public function register_unit(Unit $unit) {
        $this->_units[sha1(get_class($unit))] = $unit;
    }

    /**
     * Run a single unit
     */
    public function execute($unit_name) {

        $this->_units[sha1($unit_name)]->run();
    }

    /**
     * Wait until all units die.
     * @param type $kill
     */
    public function wait($kill = 0) {
        foreach ($this->_units as $unit) {
            $unit->wait($kill);
        }
    }

    /**
     * Tells wether or not the backend is running.
     * @return boolean
     */
    public function is_running() {
        foreach ($this->_units as $thread) {
            if ($thread->isAlive()) {
                return true;
            }
        }
        return false;
    }

    public function start() {
        if ($this->is_running())
            throw new Kohana_Exception('This backend is already running.');


        sem_acquire($this->_semaphore_id);


        foreach ($this->_units as $unit) {

            $unit->start();
        }

        $this->wait();

        sem_release($this->_semaphore_id);
    }

}

?>
