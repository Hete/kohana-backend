<?php

abstract class Unit extends Thread {

    public abstract function run();

    public function isAlive() {

        return Thread::available() && parent::isAlive();
    }

    /**
     * 
     * @param type $kill seconds until the thread is getting killed, 0 might block.
     */
    public function wait($kill = 10000) {

        $started = time();

        while ($this->isAlive()) {

            sleep(1);

            if (time() - $started >= $kill && $kill !== 0) {
                $this->kill();
            }
        }
    }

    public function start() {
        
        if (!Thread::available()) {
            $this->run();
            return;
        }


        $this->setRunnable(array($this, 'run'));

        parent::start();
    }

}

?>
