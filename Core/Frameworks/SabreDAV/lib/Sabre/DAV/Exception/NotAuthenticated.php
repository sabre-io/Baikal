<?php

namespace Sabre\DAV\Exception;

use Sabre\DAV;

/**
 * NotAuthenticated
 *
 * This exception is thrown when the client did not provide valid
 * authentication credentials.
 *
 * @copyright Copyright (C) 2007-2012 Rooftop Solutions. All rights reserved.
 * @author Evert Pot (http://www.rooftopsolutions.nl/) 
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 */
class NotAuthenticated extends DAV\Exception {

    /**
     * Returns the HTTP statuscode for this exception
     *
     * @return int
     */
    public function getHTTPCode() {

        return 401;

    }

}
