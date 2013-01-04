<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * 
 */
abstract class Kohana_Unit extends Thread {

    public $running;

    /**
     * 
     * @param string $name
     * @return \Unit
     */
    public static function factory($name) {
        $class = "Unit_$name";
        return new $class;
    }

    public function __construct() {
        parent::__construct(array($this, "_run"));
    }

    /**
     * Start the unit.
     */
    public function start() {
        if (Thread::available()) {
            parent::start();
            $this->running = TRUE;
        } else {
            $this->run();
        }
    }

    /**
     * Stop the unit.
     * @param type $_signal
     * @param type $_wait
     */
    public function stop($_signal = SIGTERM, $_wait = TRUE) {
        $this->running = FALSE;
        parent::stop($_signal, $_wait);
    }

    /**
     * Wait for this thread to end.
     * @param integer $delay microtime until the threads gets killed.
     */
    public function wait($delay = 1000) {
        $init = microtime();
        while ($this->isAlive()) {
            wait(1);
            if (microtime() - $init > $delay) {
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
    public abstract function run();

    /**
     * Interval to which this unit must be run
     */
    public abstract function interval();
}

?>
