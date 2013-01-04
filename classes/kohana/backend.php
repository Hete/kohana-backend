<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * 
 * @package Backend
 * @category Unit
 * @author Guillaume Poirier-Morency <john.doe@example.com>
 * @copyright (c) 2013, Hète.ca Inc.
 */
class Kohana_Backend {

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

    private function __construct($name) {

        // One semaphore by backend's instance name. So multiple backend from $_instances.

        $this->_name = $name;

        $this->_semaphore_id = Semaphore::instance()->get(hexdec(sha1($this->_name)));

        $this->_config = Kohana::$config->load('backend.default');

        foreach ($this->_config["units"] as $unit) {
            $this->_units[] = Unit::factory($unit);
        }
    }

    /**
     * Détermine si le Backend roule.
     * @return boolean
     */
    public function is_running() {

        foreach ($this->_units as $unit) {
            if ($unit->isAlive()) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function start() {

        Semaphore::instance()->acquire($this->_semaphore_id);

        // Starts all registered units
        foreach ($this->_units as $unit) {
            $unit->start();
        }

        // Attend que les threads terminent leurs exécutions.
        $this->wait();

        Semaphore::instance()->release($this->_semaphore_id);
    }

    public function stop() {
        if (!$this->is_running())
            throw new Kohana_Exception('Backend is not running.');

        foreach ($this->_units as $unit) {
            $unit->stop();
        }



        // Threads will expire in the start method and release the semaphore
    }

    // Wait until all threads (active units) die
    public function wait($delay = 1000) {
        if (Thread::available()) {
            foreach ($this->_units as $unit) {
                $unit->wait($delay);
            }
        }
    }

}

?>
