<?php

namespace Sabre\DAV\Exception;

/**
 * NotImplemented
 *
 * This exception is thrown when the client tried to call an unsupported HTTP method or other feature
 *
 * @copyright Copyright (C) 2007-2012 Rooftop Solutions. All rights reserved.
 * @author Evert Pot (http://www.rooftopsolutions.nl/) 
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 */
class NotImplemented extends \Sabre\DAV\Exception {

    /**
     * Returns the HTTP statuscode for this exception
     *
     * @return int
     */
    public function getHTTPCode() {

        return 501;

    }

}
