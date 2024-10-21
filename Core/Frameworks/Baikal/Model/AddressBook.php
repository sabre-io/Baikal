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

namespace Baikal\Model;

/**
* Class representing an Address Book model.
*
* This class extends the \Flake\Core\Model\Db and provides properties, methods, and functionalities
* related to managing an address book in the application. It includes metadata about the address book,
* utilities for form generation, and methods to handle linked contacts.
*/
class AddressBook extends \Flake\Core\Model\Db {
    const DATATABLE = "addressbooks";
    const PRIMARYKEY = "id";
    const LABELFIELD = "displayname";

    protected $aData = [
        "principaluri" => "",
        "displayname"  => "",
        "uri"          => "",
        "description"  => "",
    ];

    static function humanName() {
        return "Address Book";
    }

    static function icon() {
        return "icon-book";
    }

    static function mediumicon() {
        return "glyph-adress-book";
    }

    static function bigicon() {
        return "glyph2x-adress-book";
    }

    /**
    * Retrieves a base requester for fetching contacts linked to the current address book.
    *
    * This method initializes a base requester object and configures it to filter contacts
    * based on the current address book's ID, ensuring that only contacts associated with
    * this address book are retrieved.
    */
    function getContactsBaseRequester() {
        $oBaseRequester = \Baikal\Model\AddressBook\Contact::getBaseRequester();
        $oBaseRequester->addClauseEquals(
            "addressbookid",
            $this->get("id")
        );

        return $oBaseRequester;
    }

    /**
    * Creates and configures a form morphology for the current model instance. Includes validation rules
    * and help information for each form field.
    */
    function formMorphologyForThisModelInstance() {
        $oMorpho = new \Formal\Form\Morphology();

        // Add a text field for the 'uri' property with validation and popover information.
        $oMorpho->add(new \Formal\Element\Text([
            "prop"       => "uri",
            "label"      => "Address Book token ID",
            "validation" => "required,tokenid",
            "popover"    => [
                "title"   => "Address Book token ID",
                "content" => "The unique identifier for this address book.",
            ],
        ]));

        // Add a text field for the 'displayname' property with validation and popover information.
        $oMorpho->add(new \Formal\Element\Text([
            "prop"       => "displayname",
            "label"      => "Display name",
            "validation" => "required",
            "popover"    => [
                "title"   => "Display name",
                "content" => "This is the name that will be displayed in your CardDAV client.",
            ],
        ]));

        // Add a text field for the 'description' property without any validation or popover.
        $oMorpho->add(new \Formal\Element\Text([
            "prop"  => "description",
            "label" => "Description",
        ]));

        // Check if the model instance is in a "floating" state to determine form field behavior.
        if ($this->floating()) {
            $oMorpho->element("uri")->setOption(
                "help",
                "Allowed characters are digits, lowercase letters and the dash symbol '-'."
            );
        } else {
            $oMorpho->element("uri")->setOption("readonly", true);
        }

        return $oMorpho;
    }

    /**
    * Retrieves all contacts linked to the current address book and
    * ensures they are deleted before removing the address book itself.
    */
    function destroy() {
        $oContacts = $this->getContactsBaseRequester()->execute();
        foreach ($oContacts as $contact) {
            $contact->destroy();
        }

        parent::destroy();
    }
}
