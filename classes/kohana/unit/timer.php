<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Timer task!
 * 
 * $unit->every(4)->limit(12)->until('callback');
 * 
 * @package Backend
 * @author Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @copyright (c) 2013, Hète.ca Inc.
 */
abstract class Kohana_Unit_Timer extends Unit {

    private $_running = TRUE;
    protected $_every,
            $_limit,
            $_runs = 0,
            $_until;

    public function every($seconds) {
        $this->_every = (int) $seconds;
        return $this;
    }

    public function limit($runs) {
        $this->_limit = (int) $runs;
        return $this;
    }

    public function until($callback) {
        $this->_until = $callback;
        return $this;
    }

    protected function run() {
die('caa');
        while ($this->_running) {

            if ($this->_until) {
                if (call_user_func($this->_until) === TRUE) {
                    break;
                }
            }

            if ($this->_runs > $this->_limit) {
                Log::instance()->add(Log::INFO, 'Reached max runs allowed :limit.', array(':limit' => $this->_limit));
                break;
            }

            // Call timer run
            $this->timer_run();

            $this->_runs++;

            sleep($this->_every);
        }
    }

    protected abstract function timer_run();

    public function stop($_signal = SIGKILL, $_wait = false) {

        $this->_running = FALSE;

        parent::stop($_signal, $_wait);
    }

}

?>
