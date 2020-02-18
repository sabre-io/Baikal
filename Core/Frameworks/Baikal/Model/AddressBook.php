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

    function getContactsBaseRequester() {
        $oBaseRequester = \Baikal\Model\AddressBook\Contact::getBaseRequester();
        $oBaseRequester->addClauseEquals(
            "addressbookid",
            $this->get("id")
        );

        return $oBaseRequester;
    }

    function formMorphologyForThisModelInstance() {
        $oMorpho = new \Formal\Form\Morphology();

        $oMorpho->add(new \Formal\Element\Text([
            "prop"       => "uri",
            "label"      => "Address Book token ID",
            "validation" => "required,tokenid",
            "popover"    => [
                "title"   => "Address Book token ID",
                "content" => "The unique identifier for this address book.",
            ]
        ]));

        $oMorpho->add(new \Formal\Element\Text([
            "prop"       => "displayname",
            "label"      => "Display name",
            "validation" => "required",
            "popover"    => [
                "title"   => "Display name",
                "content" => "This is the name that will be displayed in your CardDAV client.",
            ]
        ]));

        $oMorpho->add(new \Formal\Element\Text([
            "prop"  => "description",
            "label" => "Description"
        ]));

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

    function destroy() {
        $oContacts = $this->getContactsBaseRequester()->execute();
        foreach ($oContacts as $contact) {
            $contact->destroy();
        }

        parent::destroy();
    }
}
