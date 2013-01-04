<?php

class Backend_Test extends Unittest_TestCase {

    public function setUp() {
        parent::setUp();
        Backend::instance()->start();
    }

    public function test_stop() {
        Backend::instance()->stop();
        $this->assertFalse(Backend::instance()->is_running());
    }

}

?>
