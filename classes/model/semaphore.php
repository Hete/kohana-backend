<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Represents a semaphore.
 * 
 * @package Backend
 * @category Model
 * @author Guillaume Poirier-Morency
 * @copyright (c) 2013, Hète.ca Inc.
 */
class Model_Semaphore extends Kohana_Model_Semaphore {

    public function rules() {
        return array(
            "key" => array(
                array("not_empty"),
                array("max_length", array(":value", 40)),
                array(array($this, "unique"), array(":field", ":value")),
            ),
            "max_acquire" => array(
            ),
        );
    }

}

?>
