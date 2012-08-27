<?php

/**
 * Backend pour site web.
 */
class Controller_Backend extends Controller {

    private $api_key = "4fe68cb34be5a23a8e87d4c1faedb3a3cb68cb93";

    public function before() {

        if (sha1($this->request->param('key')) != $this->api_key)
            throw new Kohana_Exception("Wrong api key, access denied.");

        Backend::instance()->register_all_units();
    }

    /**
     * Envoie les courriels en attente
     */
    public function action_status() {

        $this->response->body(new View('backend/status'));
    }

    public function action_start() {
        $unit_name = $this->request->param('unit');
        if ($unit_name !== NULL) {

            Backend::instance()->execute($unit_name);
        } else {
            // Start all units
            Backend::instance()->run();
        }

        $this->response->body('All units have been executed.');
    }

    public function action_stop() {
        Backend::instance()->stop();
        $this->response->body('All units have been executed.');
    }

    public function action_kill() {

        $this->response->body('All units have been executed.');
    }

}

?>
