<?php

/**
 * Represents a semaphore acquirement.
 * 
 * @package Backend
 * @category Model
 * @author Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @copyright (c) 2013, Hète.ca Inc.
 */
class Kohana_Model_Acquirement extends ORM {

    protected $_belongs_to = array(
        "semaphore" => array()
    );

    public function rules() {
        return array(
            "semaphore_id" => array(
                // Always one free acquirement
                array("range", array($this->semaphore->acquirements->count_all(), 0, $this->semaphore->max_acquire - 1))
            ),
        );
    }

}

?>
