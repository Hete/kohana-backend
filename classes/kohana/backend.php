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
     * 
     * @var array 
     */
    protected static $_instances = array();

    /**
     * 
     * @param string $name
     * @return Backend
     */
    public static function instance($name = 'default') {

        if (!array_key_exists($name, Backend::$_instances)) {

            Backend::$_instances[$name] = new Backend($name);

            // Auto-release
            register_shutdown_function(array(Backend::$_instances[$name], 'release'));
        }

        return Backend::$_instances[$name];
    }

    /**
     *
     * @var variant 
     */
    private $_semaphore_id;

    /**
     *
     * @var array 
     */
    private $_units = array();

    /**
     *
     * @var Log_Backend
     */
    private $_log_writer;

    private function __construct($name) {

        $this->_log_writer = new Log_Backend();

        $this->_semaphore_id = Semaphore::instance()->get(hexdec(sha1($name)));

        // Load all configured units
        foreach (Kohana::$config->load("backend.$name.units") as $unit) {
            $this->_units[] = Unit::factory($unit, $this->_log_writer);
        }
    }

    /**
     * Get messages in the internal log writer.
     * 
     * @return array
     */
    public function messages() {
        return $this->_log_writer->messages;
    }

    /**
     * Acquire a semaphore.
     */
    public function acquire() {
        Log::instance()->add(Log::INFO, 'Acquireing semaphore with id :id', array(":id" => $this->_semaphore_id));
        Semaphore::instance()->acquire($this->_semaphore_id);
    }

    public function acquired() {
        return Semaphore::instance()->acquired($this->_semaphore_id);
    }

    public function release() {

        Log::instance()->add(Log::INFO, 'Releasing semaphore with id :id', array(":id" => $this->_semaphore_id));
        try {
            Semaphore::instance()->release($this->_semaphore_id);
        } catch (ErrorException $ee) {
            Log::instance()->add(Log::ERROR, $ee->getMessage());
        }
    }

    /**
     * Démarre le backend.
     */
    public function start() {

        Log::instance()->attach($this->_log_writer);

        Log::instance()->add(Log::INFO, 'Acquireing a semaphore...');
        $this->acquire();

        Log::instance()->add(Log::INFO, 'Starting the backend...');
        $this->run();

        // Wait until all units dies.            
        Log::instance()->add(Log::INFO, 'Waiting after units...');
        $this->wait();

        Log::instance()->add(Log::INFO, 'Releasing a semaphore...');
        $this->release();

        Log::instance()->add(Log::INFO, 'Backend has stopped.');

        // Flush logs
        Log::instance()->write();

        // Detach writer
        Log::instance()->detach($this->_log_writer);
    }

    public function run() {
        // Starts all registered units
        foreach ($this->_units as $unit) {
            Log::instance()->add(Log::INFO, 'Starting unit :name...', array(':name' => get_class($unit)));
            $unit->start();
        }
    }

    /**
     * Backend is running if one of its units still run.
     * 
     * @return boolean
     */
    public function is_running() {
        foreach ($this->_units as $unit) {
            if ($unit->is_running()) {
                return TRUE;
            }
        }
    }

    /**
     * Stop the backend
     */
    public function stop() {
        foreach ($this->_units as $unit) {
            $unit->stop();
        }
    }

    /**
     * Wait until all units die.     *  
     */
    public function wait() {
        foreach ($this->_units as $unit) {
            $unit->wait();
        }
    }

}

?>
