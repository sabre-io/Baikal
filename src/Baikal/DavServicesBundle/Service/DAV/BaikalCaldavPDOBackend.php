<?php

namespace Baikal\DavServicesBundle\Service\DAV;

use Sabre\CalDAV\Backend\PDO as SabrePDOBackend;

class BaikalCaldavPDOBackend extends SabrePDOBackend {

    const CALDAV_OPERATION_ADD = 1;
    const CALDAV_OPERATION_MODIFY = 2;
    const CALDAV_OPERATION_DELETE = 3;

    # Proxy of Sabre\CalDAV\Backend\PDO::addChange() to allow for public usage
    public function publicAddChange() {
        return call_user_func_array(array($this, 'addChange'), func_get_args());
    }

    # Proxy of Sabre\CalDAV\Backend\PDO::getDenormalizedData() to allow for public usage
    public function publicGetDenormalizedData() {
        return call_user_func_array(array($this, 'getDenormalizedData'), func_get_args());
    }

    public function declareAddChange($calendarid, $eventuri) {
        $this->publicAddChange(
            $calendarid,
            $eventuri,
            self::CALDAV_OPERATION_ADD
        );
    }

    public function declareModifyChange($calendarid, $eventuri) {
        $this->publicAddChange(
            $calendarid,
            $eventuri,
            self::CALDAV_OPERATION_MODIFY
        );
    }

    public function declareDeleteChange($calendarid, $eventuri) {
        $this->publicAddChange(
            $calendarid,
            $eventuri,
            self::CALDAV_OPERATION_DELETE
        );
    }
}