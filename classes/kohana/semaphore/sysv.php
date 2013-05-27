<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * SysV-based semaphore implementation. You need to have php extension.
 * 
 * @package Backend
 * @category Semaphore
 */
class Kohana_Semaphore_SysV extends Semaphore {

    public function get($key) {
        return sem_get($key);
    }

    public function acquired($sem_identifier) {
        throw new Kohana_Exception('SystemV does not support acquired state.');
    }

    public function acquire($sem_identifier) {
        return sem_acquire($sem_identifier);
    }

    public function release($sem_identifier) {
        return sem_release($sem_identifier);
    }

    public function remove($sem_identifier) {
        return sem_remove($sem_identifier);
    }

}

?>
