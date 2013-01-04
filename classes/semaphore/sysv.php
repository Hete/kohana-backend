<?php

class Semaphore_SysV extends Semaphore {

    public function acquire($sem_identifier) {
        sem_acquire($sem_identifier);
    }

    public function release($sem_identifier) {
        sem_release($sem_identifier);
    }

    public function get($key) {
        sem_get($key);
    }

    public function remove($sem_identifier) {
        sem_remove($sem_identifier);
    }

}

?>
