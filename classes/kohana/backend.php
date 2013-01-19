<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Backend to deal with asynchronous tasks.
 * 
 * @package Backend
 * @author Guillaume Poirier-Morency <john.doe@example.com>
 * @copyright (c) 2013, Hète.ca Inc.
 */
class Kohana_Backend {

    protected static $_instances = array();

    /**
     * 
     * @param string $name
     * @return Backend
     */
    public static function instance($name = "default") {
        return array_key_exists($name, Backend::$_instances) ? Backend::$_instances[$name] : Backend::$_instances[$name] = new Backend($name);
    }

    private $_config;
    private $_semaphore_id;
    private $_units = array();

    private function __construct($name) {

        $this->_semaphore_id = Semaphore::instance()->get((sha1($name)));

        $this->_config = Kohana::$config->load('backend.default');

        foreach ($this->_config["units"] as $unit) {
            $this->_units[] = Unit::factory($unit);
        }
    }

    /**
     * Logging system.
     * 
     * @param type $level
     * @param type $message
     * @param array $values
     */
    public function log($level, $message, array $values = NULL) {
        echo "<li>" . __($message, $values) . "</li>";
        Log::instance()->add($level, $message, $values);
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

        echo "<ul>";

        // Backend is already started
        if (Semaphore::instance()->acquired($this->_semaphore_id)) {

            $this->log(Log::NOTICE, "Backend is already started.");
            return;
        }

        $this->acquire();

        // Auto release
        register_shutdown_function(array($this, "release"));

        $this->log(Log::INFO, "Starting the backend...");
        $this->run();

        // Units runs in their own process, managing their own resources.
        // Wait until all units dies.
        $this->log(Log::INFO, "Waiting after units...");
        $this->wait();

        // Release the semaphore
        $this->release();

        $this->log(Log::INFO, "Backend has stopped.");

        echo "</ul>";
    }

    public function run() {

        // Starts all registered units
        foreach ($this->_units as $unit) {
            try {
                $this->log(Log::INFO, "Starting unit :name...", array(":name" => get_class($unit)));
                $unit->start();
            } catch (Exception $e) {
                $this->log(Log::ERROR, $e->getMessage());
                // Execute next unit
                continue;
            }
        }
    }

    private function acquire() {
        $this->log(Log::INFO, "Acquireing semaphore with id :id", array(":id" => $this->_semaphore_id));
        return Semaphore::instance()->acquire($this->_semaphore_id);
    }

    private function release() {
        $this->log(Log::INFO, "Releasing semaphore with id :id", array(":id" => $this->_semaphore_id));
        return Semaphore::instance()->release($this->_semaphore_id);
    }

    /**
     * Release all acquirements. Single releasing is private.
     */
    public function remove() {
        $this->log(Log::INFO, "Releasing all acquirements with semaphore with id :id", array(":id" => $this->_semaphore_id));
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
