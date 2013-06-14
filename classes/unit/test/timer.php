<?php

class Unit_Test_Timer extends Unit_Timer {

    protected function timer_run() {
        echo 'Mcaca';
        Log::instance()->add(Log::INFO, "Hey, I'm running every $this->_every for $this->_limit runs :)");
    }

}

?>
