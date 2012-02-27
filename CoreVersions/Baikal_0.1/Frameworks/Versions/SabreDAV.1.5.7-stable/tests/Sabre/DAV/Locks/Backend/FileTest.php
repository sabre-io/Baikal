<?php

require_once 'Sabre/TestUtil.php';

class Sabre_DAV_Locks_Backend_FileTest extends Sabre_DAV_Locks_Backend_AbstractTest {

    function getBackend() {

        Sabre_TestUtil::clearTempDir();
        $backend = new Sabre_DAV_Locks_Backend_File(SABRE_TEMPDIR . '/lockdb');
        return $backend;

    }


    function tearDown() {

        Sabre_TestUtil::clearTempDir();

    }

}
