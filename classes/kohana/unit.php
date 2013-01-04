<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * 
 */
abstract class Kohana_Unit extends Thread {

    private $running;

    /**
     * 
     * @param string $name
     * @return \Unit
     */
    public static function factory($name) {
        $class = "Unit_$name";
        return new $class;
    }

    public function start() {
        if (Thread::available()) {
            $this->setRunnable(array($this, "_run"));
            parent::start();
        } else {
            $this->run();
        }
        $this->running = TRUE;
    }

    public function wait($delay = 1000) {
        while ($this->isAlive()) {
            if (microtime() - $init > $delay) {
                // We kill the 
                parent::stop();
            }
            wait(1);
        }
    }

    private function _run() {
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
