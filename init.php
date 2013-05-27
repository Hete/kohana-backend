<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * To run the backend from CLI:
 * 
 *     php --backend index.php
 * 
 * @package Backend
 * @author Hète.ca Team
 * @copyright (c) 2013, Hète.ca Inc.
 */
if (Kohana::$is_cli) {
    if (CLI::options('backend')) {
        Backend::instance()->start();
        exit(0);
    }
}
?>
