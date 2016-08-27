<?php

namespace Baikal\Service;

use Baikal\Domain\User;
use Sabre\CardDAV\Backend\BackendInterface as CardBackend;

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

    function getByUserNameAndAddressbookId($userName, $addressbookId) {
        $addressbooks = $this->cardBackend->getAddressbooksForUser('principals/' . $userName);
        foreach ($addressbooks as $addressbook) {
            if ($addressbook['id'] == $addressbookId) {
               return $addressbook;
            }
        }
    }
}
