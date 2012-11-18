<?php

namespace Sabre\DAV\Exception;

/**
 * Conflict
 *
 * A 409 Conflict is thrown when a user tried to make a directory over an existing
 * file or in a parent directory that doesn't exist.
 *
 * @copyright Copyright (C) 2007-2012 Rooftop Solutions. All rights reserved.
 * @author Evert Pot (http://www.rooftopsolutions.nl/) 
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 */
class Conflict extends \Sabre\DAV\Exception {

    /**
     * Returns the HTTP statuscode for this exception
     *
     * @return int
     */
    public function getHTTPCode() {

        return 409;

    }

}
