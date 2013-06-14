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
 * @category Units
 * @author Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @copyright (c) 2013, Hète.ca Inc.
 */
abstract class Kohana_Unit extends Thread {    
   
    /**
     * 
     * @param string $name
     * @return \Unit
     */
    public static function factory($name) {
        $class = "Unit_$name";
        return new $class();
    }

    /**
     * 
     * @param runnable $runnable defaulted to $this->run.
     */
    public function __construct($runnable = NULL) {

        if ($runnable === NULL) {
            $runnable = array($this, 'run');
        }

        parent::__construct($runnable);
    }

    /**
     * Start the unit.
     */
    public function start() {

        $this->before();

        if (static::available()) {
            parent::start();
        } else {
            $this->run();
        }

        $this->after();
    }

    public function stop($_signal = SIGKILL, $_wait = false) {
        static::available() AND parent::stop($_signal, $_wait);
    }

    public function is_running() {
        return static::available() ? $this->isAlive() : FALSE;
    }

    /**
     * Wait for unit to finish.
     */
    public function wait() {
        while ($this->is_running()) {
            sleep(1);
        }
    }

    protected function before() {
        Log::instance()->add(Log::INFO, 'Unit :unit has started its execution', array(':unit' => get_class($this)));
    }

    /**
     * Code executed in the unit
     */
    protected abstract function run();

    protected function after() {

        Log::instance()->add(Log::INFO, 'Unit :unit has stopped executing.', array(':unit' => get_class($this)));

        // Flush logs when unit has run
        Log::instance()->write();
    }

}

?>
