<?php

namespace Sabre\DAV\Exception;

use Sabre\DAV;

/**
 * Payment Required
 *
 * The PaymentRequired exception may be thrown in a case where a user must pay
 * to access a certain resource or operation.
 *
 * @copyright Copyright (C) 2007-2012 Rooftop Solutions. All rights reserved.
 * @author Evert Pot (http://www.rooftopsolutions.nl/)
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 */
class PaymentRequired extends DAV\Exception {

    /**
     * Returns the HTTP statuscode for this exception
     *
     * @return int
     */
    public function getHTTPCode() {

        return 402;

    }

}
