<?php

abstract class Kohana_Backend {

    protected static $_instances = array();

    /**
     * 
     * @param type $name
     * @return Kohana_Backend
     */
    public static function instance($name = "default") {
        return array_key_exists($name, Backend::$_instances) ? Backend::$_instances[$name] : Backend::$_instances[$name] = new Backend();
    }

    private $_config;
    private $_units = array();

    private function __construct() {
        $this->_config = Kohana::$config->load('backend.default');
    }

    public function load_units() {
        foreach ($this->_config["units"] as $unit) {
            $this->register_unit(new $unit);
        }
    }

    public function register_unit(Unit $unit) {
        $this->_units[sha1($unit->name())] = $unit;
    }

    /**
     * Run a single unit
     */
    public function execute($unit_name) {

        $this->_units[sha1($unit_name)]->run();
    }

    public function start() {

        $threads = array();
        $index = 0;

        foreach ($this->_units as $unit) {

            $unit = $unit->name();

            function execute_unit($name) {

                Backend::instance()->execute($name);
            }

            if (Thread::available()) {

                $threads[$index] = new Thread('execute_unit');
                $threads[$index]->start($unit);

                while (!empty($threads)) {
                    foreach ($threads as $index => $thread) {
                        if (!$thread->isAlive()) {
                            unset($threads[$index]);
                        }
                    }
                    // let the CPU do its work
                    sleep(1);
                }
            } else {
                execute_unit($unit);
            }
        }
    }

}

?>
