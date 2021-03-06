<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * 
 * @package Backend
 * @category Semaphore
 * @author Guillaume Poirier-Morency
 * @copyright (c) 2013, Hète.ca Inc.
 */
abstract class Kohana_Semaphore {

    static $default_driver = "ORM";

    /**
     * 
     * @param type $name
     * @return \Semaphore
     */
    public static function instance($name = NULL) {

        $name = $name === NULL ? static::$default_driver : $name;

        $class = "Semaphore_" . $name;
        return new $class;
    }

    /**
     * Obtain a semaphore.
     */
    abstract function get($key);

    /**
     * Acquire the semaphore.
     */
    abstract function acquire($sem_identifier);

    /**
     * Tells if a semaphore is currently acquired.
     */
    abstract function acquired($sem_identifier);

    abstract function release($sem_identifier);

    abstract function remove($sem_identifier);
}

?>
