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
abstract class Kohana_Unit {

    /**
     *
     * @var Log_Writer
     */
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

        $this->_log_writer = $log_writer;
    }

    /**
     * Start the unit.
     */
    public function start() {

        Log::instance()->attach($this->_log_writer);

        $this->run();

        // Flush writer
        Log::instance()->write();
    }

    /**
     * Code executed in the unit
     */
    protected abstract function run();
}

?>
