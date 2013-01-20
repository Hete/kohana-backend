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
                // Respects maximum acquirements
                array("smaller", array($this->semaphore->acquirements->where("id", "!=", $this->pk())->count_all(), $this->semaphore->max_acquire))
            ),
        );
    }

}

?>
