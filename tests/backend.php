<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * 
 * @package Backend
 * @category Tests
 * @author Hète.ca Team
 * @copyright (c) 2013, Hète.ca Inc.
 */
class Backend_Test extends Unittest_TestCase {

    public function test_execute() {

        $this->assertTrue(Thread::available());

        Backend::instance('test')->start();

        Backend::instance('test')->start();
    }

}

?>
