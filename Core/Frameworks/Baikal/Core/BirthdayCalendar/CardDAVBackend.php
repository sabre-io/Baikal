<?php

#################################################################
#  Copyright notice
#
#  (c) 2013 Jérôme Schneider <mail@jeromeschneider.fr>
#  All rights reserved
#
#  http://sabre.io/baikal
#
#  This script is part of the Baïkal Server project. The Baïkal
#  Server project is free software; you can redistribute it
#  and/or modify it under the terms of the GNU General Public
#  License as published by the Free Software Foundation; either
#  version 2 of the License, or (at your option) any later version.
#
#  The GNU General Public License can be found at
#  http://www.gnu.org/copyleft/gpl.html.
#
#  This script is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  This copyright notice MUST APPEAR in all copies of the script!
#################################################################

namespace Baikal\Core\BirthdayCalendar;

/**
 * CardDAV backend that keeps the birthday calendars up to date.
 *
 * Every change is first stored as usual. Errors while updating the
 * birthday calendar are logged and never fail the CardDAV request; the
 * calendar can be rebuilt at any time.
 */
class CardDAVBackend extends \Sabre\CardDAV\Backend\PDO {
    /**
     * @var BirthdayService
     */
    protected $birthdayService;

    function __construct(\PDO $pdo, BirthdayService $birthdayService) {
        parent::__construct($pdo);
        $this->birthdayService = $birthdayService;
    }

    function createCard($addressBookId, $cardUri, $cardData) {
        $result = parent::createCard($addressBookId, $cardUri, $cardData);
        $this->notify(function () use ($addressBookId, $cardUri, $cardData) {
            $this->birthdayService->onCardChanged($addressBookId, $cardUri, $cardData);
        });

        return $result;
    }

    function updateCard($addressBookId, $cardUri, $cardData) {
        $result = parent::updateCard($addressBookId, $cardUri, $cardData);
        $this->notify(function () use ($addressBookId, $cardUri, $cardData) {
            $this->birthdayService->onCardChanged($addressBookId, $cardUri, $cardData);
        });

        return $result;
    }

    function deleteCard($addressBookId, $cardUri) {
        $result = parent::deleteCard($addressBookId, $cardUri);
        $this->notify(function () use ($addressBookId, $cardUri) {
            $this->birthdayService->onCardDeleted($addressBookId, $cardUri);
        });

        return $result;
    }

    function deleteAddressBook($addressBookId) {
        # The owner can only be looked up while the address book still exists
        $principalUri = null;
        $this->notify(function () use ($addressBookId, &$principalUri) {
            $principalUri = $this->birthdayService->getPrincipalUriForAddressBook($addressBookId);
        });

        parent::deleteAddressBook($addressBookId);

        if ($principalUri !== null) {
            $this->notify(function () use ($principalUri, $addressBookId) {
                $this->birthdayService->onAddressBookDeleted($principalUri, $addressBookId);
            });
        }
    }

    /**
     * Runs a birthday calendar update, logging instead of throwing errors.
     *
     * @return void
     */
    protected function notify(callable $update) {
        try {
            $update();
        } catch (\Throwable $e) {
            # Also catches PHP errors, as the card is already stored and
            # the request must not fail because of the birthday calendar
            error_log("Error updating birthday calendar: " . $e);
        }
    }
}
