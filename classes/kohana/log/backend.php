<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * 
 * @package Backend
 * @author Hète.ca Team
 * @copyright (c) 2013, Hète.ca Inc.
 */
class Kohana_Log_Backend extends Log_Writer {

    public $messages = array();

    public function write(array $messages) {
        $this->messages += $messages;
    }

}

?>
