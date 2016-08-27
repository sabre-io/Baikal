<?php

namespace Baikal\Service;

use Baikal\Domain\User;
use Sabre\CardDAV\Backend\BackendInterface as CardBackend;

/**
 * UserRepository implementation using PDO
 */
class AddressbookService {

    /**
     * @var CardBackend
     */
    private $cardBackend;

    function __construct(CardBackend $cardBackend) {

        $this->cardBackend = $cardBackend;
        
    }

    /**
     * Creates a new Addressbook for a new User
     */
    function provision(User $user) {

        $this->cardBackend->createAddressBook($user->getPrincipalUri(), 'default', [
            '{DAV:}displayname' => 'Default Contacts'
        ]);
    }

}
