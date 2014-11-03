<?php

namespace Baikal\DavServicesBundle\Service\DAV;

use Sabre\CalDAV\Backend\PDO as SabrePDOBackend;

class BaikalCaldavPDOBackend extends SabrePDOBackend {

    # Proxy of Sabre\CalDAV\Backend\PDO::addChange() to allow for public usage
    public function publicAddChange() {
        return call_user_func_array(array($this, 'addChange'), func_get_args());
    }

    # Proxy of Sabre\CalDAV\Backend\PDO::getDenormalizedData() to allow for public usage
    public function publicGetDenormalizedData() {
        return call_user_func_array(array($this, 'getDenormalizedData'), func_get_args());
    }
}