<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Represents a semaphore.
 * 
 * @package Backend
 * @category Model
 * @author Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @copyright (c) 2013, Hète.ca Inc.
 */
class Kohana_Model_Semaphore extends ORM {

    protected $_has_many = array(
        "acquirements" => array()
    );

    public function rules() {
        return array(
            "key" => array(
                array("not_empty"),
                array("max_length", array(":value", 40)),
                array(array($this, "unique"), array(":field", ":value")),
            ),
            "max_acquire" => array(
                array("range", array(":value", 1, PHP_INT_MAX))
            ),
        );
    }

}

?>
