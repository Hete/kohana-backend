<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * To run the backend from CLI:
 * 
 *     php --backend index.php
 * 
 * Backend will execute as a shutdown function.
 * 
 * @package Backend
 * @author Hète.ca Team
 * @copyright (c) 2013, Hète.ca Inc.
 */
if (Kohana::$is_cli) {

    function _backend() {

        $options = CLI::options('backend', 'action');

        $group = Arr::get($options, 'backend');

        switch ($command = Arr::get($options, 'action')) {
            case 'start':
            case NULL:
                Backend::instance($group)->start();
            case 'stop':
                Backend::instance($group)->stop();
            case 'release':
                Backend::instance($group)->release();
                exit(0);
                break;
            default:
                echo "'$command' is not a valid command.\n\tphp index.php --backend=<command>\n\t<command> can be 'start', 'stop' or 'release'\n";
                exit(1);
        }
    }

    // Run at shutdown
    register_shutdown_function('_backend');
}
?>
