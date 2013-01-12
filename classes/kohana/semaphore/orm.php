<?php

defined('SYSPATH') or die('No direct script access.');

class Kohana_Semaphore_ORM extends Semaphore {

    public function acquire($sem_identifier) {

        // Load the semaphore
        $semaphore = ORM::factory("semaphore", $sem_identifier);

        if (!$semaphore->loaded()) {
            // Creating a new semaphore
            $semaphore->create();
        }

        while ($semaphore->acquirements->count_all() >= $semaphore->max_acquire) {
            wait(1);
            $semaphore->reload();
        }
        $acquirement = ORM::factory("acquirement");
        $acquirement->semaphore = $semaphore;
        $acquirement->create();
    }

    public function acquired($sem_identifier) {
        $semaphore = ORM::factory("semaphore", $sem_identifier);
        return (bool) $semaphore->acquirements->count_all() >= $semaphore->max_acquire;
    }

    public function get($key, $max_acquire = 1) {
        $semaphore = ORM::factory("semaphore", array("key" => $key));
        $semaphore->key = $key;
        $semaphore->max_acquire = $max_acquire;
        return $semaphore->save()->pk();
    }

    public function release($sem_identifier) {
        ORM::factory("semaphore", $sem_identifier)->acquirements->find()->delete();
    }

    public function remove($sem_identifier) {
        ORM::factory("semaphore", $sem_identifier)->delete();
    }

}

?>
