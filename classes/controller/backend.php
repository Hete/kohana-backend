<?php

/**
 * Backend pour site web.
 */
class Controller_Backend extends Controller {

    private $api_key = "4fe68cb34be5a23a8e87d4c1faedb3a3cb68cb93";

    /**
     * Envoie les courriels en attente
     */
    public function action_index() {
        if (sha1($this->request->param('key')) != $this->api_key)
            throw new Kohana_Exception("Wrong api key, access denied.");
        
        Backend::instance()->register_unit(new Unit_Groupe());

        Backend::instance()->start();
    }

}

?>
