<?php

namespace Baikal\Service;

use Baikal\Domain\User;
use Sabre\CardDAV\Backend\BackendInterface as CardBackend;
use Sabre\DAV\UUIDUtil;

class AddressbookService {

    /**
     * @var CardBackend
     */
    private $cardBackend;

    function __construct(CardBackend $cardBackend) {

        $this->cardBackend = $cardBackend;
        
    }

    /**
     * Creates the default address book for a new user
     */
    function provision(User $user) {

        $this->cardBackend->createAddressBook($user->getPrincipalUri(), UUIDUtil::getUUID(), [
            '{DAV:}displayname' => 'Default Contacts'
        ]);
    }

    /**
     * Creates a new address book for a user
     */
    function createAddressBook(User $user, $displayName, $addressBookDescription) {

        $this->cardBackend->createAddressBook($user->getPrincipalUri(), UUIDUtil::getUUID(), [
            '{DAV:}displayname'                                       => $displayName,
            '{urn:ietf:params:xml:ns:carddav}addressbook-description' => $addressBookDescription,
        ]);
    }

    function getByUserNameAndAddressbookId($userName, $addressbookId) {
        $addressbooks = $this->cardBackend->getAddressbooksForUser('principals/' . $userName);
        foreach ($addressbooks as $addressbook) {
            if ($addressbook['id'] == $addressbookId) {
                $addressbook['path'] = 'addressbooks/' . $userName . '/' . $addressbook['uri'] . '/';
                return $addressbook;
            }
        }
    }
}
