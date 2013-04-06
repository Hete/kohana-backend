<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Unit for backend. Use with care. You only need to implement run() and 
 * interval() methods. 
 * 
 * run method is the executable code you need to schedule.
 * 
 * interval tells in seconds the time between each executions.
 * 
 * @package Backend
 * @category Unit
 * @author Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @copyright (c) 2013, Hète.ca Inc.
 */
abstract class Kohana_Unit extends Thread {

    public $running;
    protected $interval = 3600;
    private $_log_writer;

    /**
     * 
     * @param string $name
     * @return \Unit
     */
    public static function factory($name, Log_Writer $log_writer) {
        $class = "Unit_$name";
        return new $class($log_writer);
    }

    public function __construct(Log_Writer $log_writer) {

        parent::__construct(array($this, "_run"));

        $this->_log_writer = $log_writer;
    }

    /**
     * Start the unit.
     */
    public function start() {

        Log::instance()->attach($this->_log_writer);

        if (Thread::available()) {
            parent::start();
            $this->running = TRUE;
        } else {
            $this->run();
        }

        // Flush writer
        Log::instance()->write();
    }

    /**
     * Stop the unit.
     * 
     * @param type $_signal
     * @param type $_wait
     */
    public function stop($_signal = SIGTERM, $_wait = TRUE) {
        $this->running = FALSE;
        parent::stop($_signal, $_wait);
    }

    /**
     * Wait for this unit to stop. Kill it if $delay is passed.
     * 
     * @param integer $kill microtime until the threads gets killed. If NULL, 
     * threads never gets killed and will terminate by itself.
     */
    public function wait($kill = FALSE, $wait = 1) {
        $init = microtime();
        while ($this->isAlive()) {
            wait($wait);
            if ($kill !== FALSE && (microtime() - $init > $kill)) {
                // We kill the thread
                parent::kill();
            }
        }
    }

    /**
     * Internal running function.
     */
    protected function _run() {
        while ($this->running) {
            $init = mircotime();
            $this->run();
            $delay = microtime() - $init;
            $true_delay = $this->interval() - $delay;
            sleep($true_delay >= 0 ? $true_delay : 0);
        }
    }

    /**
     * Code executed in the unit
     */
    protected abstract function run();
}

?>
