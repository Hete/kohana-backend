<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Backend to deal with asynchronous tasks.
 * 
 * @package Backend
 * @author Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @copyright (c) 2013, Hète.ca Inc.
 */
class Kohana_Backend {

    /**
     * Instances for multiple backends.
     * @var array 
     */
    protected static $_instances = array();

    /**
     * 
     * @param string $name
     * @return Backend
     */
    public static function instance($name = "default", Log_Writer $log_writer = NULL) {
        return array_key_exists($name, Backend::$_instances) ? Backend::$_instances[$name] : Backend::$_instances[$name] = new Backend($name);
    }

    private $_config;
    private $_semaphore_id;
    private $_units = array();

    /**
     *
     * @var Log_Backend
     */
    private $_log_writer;

    private function __construct($name, Log_Writer $log_writer = NULL) {

        if ($log_writer === NULL) {
            $log_writer = new Log_Backend();
        }

        $this->_log_writer = $log_writer;

        $this->_semaphore_id = Semaphore::instance()->get(sha1($name));

        $this->_config = Kohana::$config->load('backend.default');

        // Load all configured units
        foreach ($this->_config["units"] as $unit) {
            $this->_units[] = Unit::factory($unit, $log_writer);
        }
    }

    /**
     * 
     * @return \Log_Writer
     */
    public function log_writer() {
        return $this->_log_writer;
    }

    public function messages() {
        return $this->_log_writer->messages;
    }

    /**
     * Détermine si le Backend roule.
     * @return boolean
     */
    public function is_running() {

        if (Thread::available()) {
            foreach ($this->_units as $unit) {
                if ($unit->isAlive()) {
                    return TRUE;
                }
            }
        }

        return FALSE;
    }

    /**
     * Démarre le backend.
     */
    public function start() {

        // Backend is already started
        if (Semaphore::instance()->acquired($this->_semaphore_id)) {
            Log::instance()->add(Log::NOTICE, "Backend is already started.");
            return;
        }

        Log::instance()->attach($this->_log_writer);

        $this->acquire();

        // Auto release
        register_shutdown_function(array($this, "release"));

        Log::instance()->add(Log::INFO, "Starting the backend...");
        $this->run();

        // Wait until all units dies.            
        Log::instance()->add(Log::INFO, "Waiting after units...");
        $this->wait();

        // Release the semaphore
        $this->release();

        Log::instance()->add(Log::INFO, "Backend has stopped.");

        // Flush logs
        Log::instance()->write();

        Log::instance()->detach($this->_log_writer);
    }

    public function run() {
        // Starts all registered units
        foreach ($this->_units as $unit) {
            Log::instance()->add(Log::INFO, "Starting unit :name...", array(":name" => get_class($unit)));
            $unit->start();
        }
    }

    private function acquire() {
        Log::instance()->add(Log::INFO, "Acquireing semaphore with id :id", array(":id" => $this->_semaphore_id));
        return Semaphore::instance()->acquire($this->_semaphore_id);
    }

    private function release() {
        Log::instance()->add(Log::INFO, "Releasing semaphore with id :id", array(":id" => $this->_semaphore_id));
        return Semaphore::instance()->release($this->_semaphore_id);
    }

    /**
     * Release all acquirements. Single releasing is private.
     */
    public function remove() {
        Log::instance()->add(Log::INFO, "Releasing all acquirements with semaphore with id :id", array(":id" => $this->_semaphore_id));
        return Semaphore::instance()->remove($this->_semaphore_id);
    }

    /**
     * Stop the backend
     * @throws Kohana_Exception
     */
    public function stop() {
        if (Thread::available()) {
            foreach ($this->_units as $unit) {
                $unit->stop();
            }
        }
    }

    // Wait until all threads (active units) die
    /**
     * 
     * @param type $kill
     * @param type $wait_per_unit
     */
    public function wait($kill = FALSE, $wait_per_unit = 1) {
        if (Thread::available()) {
            foreach ($this->_units as $unit) {
                $unit->wait($kill, $wait_per_unit);
            }
        }
    }

}

?>
