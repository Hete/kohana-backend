<?php

if (Kohana::$is_cli AND Valid::not_empty(CLI::options("backend"))) {
    Backend::instance()->start();
    exit(0);
}
?>
