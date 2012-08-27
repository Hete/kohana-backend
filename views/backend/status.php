<?php
defined('SYSPATH') or die('No direct script access.');

// Show the status of the backend followed by the status of a unit

?>

Backend is running : <?php echo Backend::instance()->is_running() ? "Yes" : "No" ?><br />

Thread available : <?php echo Thread::available() ? "Yes" : "No" ?>
