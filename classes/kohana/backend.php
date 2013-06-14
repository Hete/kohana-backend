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
     * Get an instance of Backend.
     * 
     * @param string $name
     * @return Backend
     */
    public static function instance($name = 'default') {

        if (Arr::get(Backend::$_instances, $name) === NULL) {

            Backend::$_instances[$name] = new Backend($name);

            // Auto-release
            register_shutdown_function(array(Backend::$_instances[$name], 'release'));
        }

        return Backend::$_instances[$name];
    }

    /**
     * Semaphore id.
     * 
     * @var variant 
     */
    private $semaphore_id;

    /**
     * Units to run.
     * 
     * @var array 
     */
    private $_units = array();

    /**
     * Log writer
     * 
     * @var \Log_Writer
     */
    private $writer;

    protected function __construct($name) {

        $this->writer = new Log_StdOut();

        $this->semaphore_id = Semaphore::instance()->get(hexdec(sha1($name)));

        $units = (array) Kohana::$config->load("backend.$name.units");

        // Load all configured units
        foreach ($units as $unit) {
            $this->_units[] = Unit::factory($unit);
        }
    }

    /**
     * Get or set log writer.
     * 
     * @param Log_Writer $writer
     * @return \Kohana_Backend
     */
    public function writer(Log_Writer $writer = NULL) {

        if ($writer === NULL) {
            return $this->writer;
        }

        $this->writer = $writer;

        return $this;
    }

    /**
     * Acquire a semaphore.
     */
    public function acquire() {
        Semaphore::instance()->acquire($this->semaphore_id);
    }

    /**
     * Tells if the semaphore is acquired.
     * 
     * @return boolean
     */
    public function acquired() {
        return Semaphore::instance()->acquired($this->semaphore_id);
    }

    /**
     * Release the semaphore.
     */
    public function release() {
        try {
            Semaphore::instance()->release($this->semaphore_id);
        } catch (ErrorException $ee) {
            Log::instance()->add(Log::ERROR, $ee->getMessage());
        }
    }

    /**
     * Starts the backend.
     */
    public function start() {

        $write_on_add = Log::$write_on_add;
        Log::$write_on_add = TRUE;

        Log::instance()->add(Log::INFO, 'Acquireing semaphore with id :id...', array(":id" => $this->semaphore_id));
        $this->acquire();

        Log::instance()->add(Log::INFO, 'Starting the backend...');
        $this->run();

        // Wait until all units dies.            
        Log::instance()->add(Log::INFO, 'Waiting after units...');
        $this->wait();

        Log::instance()->add(Log::INFO, 'Releasing semaphore with id :id...', array(":id" => $this->semaphore_id));
        $this->release();

        Log::instance()->add(Log::INFO, 'Backend has stopped.');

        // Detach writer
        Log::instance()->detach($this->writer);

        Log::$write_on_add = $write_on_add;
    }

    /**
     * Run all units.
     */
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
