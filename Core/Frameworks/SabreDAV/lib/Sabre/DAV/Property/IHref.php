<?php

namespace Sabre\DAV\Property;

/**
 * IHref interface
 *
 * Any property implementing this interface can expose a related url.
 * This is used by certain subsystems to aquire more information about for example
 * the owner of a file
 *
 * @copyright Copyright (C) 2007-2012 Rooftop Solutions. All rights reserved.
 * @author Evert Pot (http://www.rooftopsolutions.nl/)
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 */
interface IHref {

    /**
     * getHref
     *
     * @return string
     */
    function getHref();

}
