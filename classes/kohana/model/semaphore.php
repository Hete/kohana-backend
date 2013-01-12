<?php

/**
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
    
}

?>
