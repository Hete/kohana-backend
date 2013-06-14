<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Sample unit
 * 
 * @package Backend
 * @category Units
 * @author Hète.ca Team
 * @copyright (c) 2013, Hète.ca Inc.
 */
class Unit_Test_Test2 extends Unit {

    public function run() {
        foreach (Arr::range(10, 100) as $i) {
            echo "Look, a number $i at " . microtime() . "\n";
        }
    }

}

?>
