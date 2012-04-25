<?php

class Sabre_DAV_ClientMock extends Sabre_DAV_Client {

    public $response;

    public $url;
    public $curlSettings;

    protected function curlRequest($url, $curlSettings) {

        $this->url = $url;
        $this->curlSettings = $curlSettings; 
        return $this->response;

    }

    /**
     * Just making this method public
     */
    public function getAbsoluteUrl($url) {

        return parent::getAbsoluteUrl($url);

    }

}
