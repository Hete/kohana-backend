<?php

defined('SYSPATH') or die('No direct script access.');

class Kohana_Semaphore_ORM extends Semaphore {

    public function acquire($sem_identifier) {
        $semaphore = ORM::factory("semaphore", $sem_identifier);
        while ($semaphore->acquired) {
            wait(1);
            $semaphore->reload();
        }
        $semaphore->acquired = TRUE;
        $semaphore->update();
        return $semaphore->reload()->acquired;
    }

    public function acquired($sem_identifier) {
        return ORM::factory("semaphore", $sem_identifier)->acquired;
    }

    public function get($key = NULL) {
        return ORM::factory("semaphore", array("key" => $key))->save()->pk();
    }

    public function release($sem_identifier) {
        ORM::factory("semaphore", $sem_identifier)->set("acquired", FALSE)->update();
    }

    public function remove($sem_identifier) {
        ORM::factory("semaphore", $sem_identifier)->remove();
    }

}

?>
