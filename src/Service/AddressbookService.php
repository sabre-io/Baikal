<?php

namespace Baikal\Service;

use Baikal\Domain\User;
#use Generator;
use PDO;
#use Sabre\CalDAV\Backend\BackendInterface as CalBackend;
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
    function createDefault(User $user) {

        $this->cardBackend->createAddressBook($user->getPrincipalUri(), 'default', [
            '{DAV:}displayname'                                       => 'Default Addressbook',
            '{urn:ietf:params:xml:ns:carddav}addressbook-description' => 'Default Description',
        ]);
    }

}
