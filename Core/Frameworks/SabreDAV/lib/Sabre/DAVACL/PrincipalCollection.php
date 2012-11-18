<?php

namespace Sabre\DAVACL;

/**
 * Principals Collection
 *
 * This collection represents a list of users.
 * The users are instances of Sabre\DAVACL\Principal
 *
 * @copyright Copyright (C) 2007-2012 Rooftop Solutions. All rights reserved.
 * @author Evert Pot (http://www.rooftopsolutions.nl/)
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 */
class PrincipalCollection extends AbstractPrincipalCollection {

    /**
     * This method returns a node for a principal.
     *
     * The passed array contains principal information, and is guaranteed to
     * at least contain a uri item. Other properties may or may not be
     * supplied by the authentication backend.
     *
     * @param array $principal
     * @return \Sabre\DAV\INode
     */
    public function getChildForPrincipal(array $principal) {

        return new Principal($this->principalBackend, $principal);

    }

}
