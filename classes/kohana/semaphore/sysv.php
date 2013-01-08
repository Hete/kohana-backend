<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * SysV-based semaphore implementation. You need to have php extension.
 * 
 * @package Backend
 * @category Semaphore
 */
class Semaphore_SysV extends Semaphore {

    public function get() {
        return sem_get(uniqid());
    }

    public function acquired($sem_identifier) {
        throw Kohana_Exception("SysV does not support acquired state.");
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
