<?php

namespace Sabre\DAV;

/**
 * The IExtendedCollection interface.
 *
 * This interface can be used to create special-type of collection-resources
 * as defined by RFC 5689.
 *
 * @copyright Copyright (C) 2007-2012 Rooftop Solutions. All rights reserved.
 * @author Evert Pot (http://www.rooftopsolutions.nl/)
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 */
interface IExtendedCollection extends ICollection {

    /**
     * Creates a new collection
     *
     * @param string $name
     * @param array $resourceType
     * @param array $properties
     * @return void
     */
    function createExtendedCollection($name, array $resourceType, array $properties);

}

