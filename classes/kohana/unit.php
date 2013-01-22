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
        echo "<ul>";
        if (Thread::available()) {
            parent::start();
            $this->running = TRUE;
        } else {
            $this->run();
        }
        echo "</ul>";
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
     * Alias for Backend::log
     * 
     * @see Backend::log
     */
    public function log($level, $message, array $values = NULL) {
        Backend::log($level, $message, $values);
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
}

?>
