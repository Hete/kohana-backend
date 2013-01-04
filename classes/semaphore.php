<?php

abstract class Semaphore {

    static $default_driver = "File";

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
    
    abstract function get($key);

    abstract function acquire($sem_identifier);

    abstract function release($sem_identifier);
    
    abstract function remove($sem_identifier);
}

?>
