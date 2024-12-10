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

namespace BaikalAdmin\Service;

use Baikal\Model\AddressBook;

class AddressBooks extends \BaikalAdmin\Service\Service {
   /**
    *  Fetches all address books and returns them as an array
    * 
    *  @return array
    */
   public function getAll(): array {
        # Render list of address books
        $aAddressBooks = [];
        $oAddressBooks = \Baikal\Model\User::getAddressBooksBaseRequester()->execute();
        foreach ($oAddressBooks as $addressbook) {
            $aAddressBooks[] = [
                "linkedit"    => \BaikalAdmin\Controller\User\AddressBooks::linkEdit($addressbook),
                "linkdelete"  => \BaikalAdmin\Controller\User\AddressBooks::linkDelete($addressbook),
                "davuri"      => \BaikalAdmin\Controller\User\AddressBooks::getDavUri($addressbook),
                "icon"        => $addressbook->icon(),
                "label"       => $addressbook->label(),
                "contacts"    => $addressbook->getContactsBaseRequester()->count(),
                "description" => $addressbook->get("description"),
            ];
        }

       return $aAddressBooks;
   }

   /**
    * Render the view for the address books page.
    * 
    * @return string
    */
   public function render(\BaikalAdmin\View\User\AddressBooks $oView
                           \Baikal\Model\User $oUser, 
                           array $aAddressBooks, 
                           array $aMessages, 
                           \Formal\Form $oForm,
                           \BaikalAdmin\Controller\User\AddressBooks $controller
                           ): string {

       # User
       $oView->setData("user", $oUser);

       $oView->setData("addressbooks", $aAddressBooks);

       # Messages
       $sMessages = implode("\n", $aMessages);
       $oView->setData("messages", $sMessages);

       if ($controller->actionNewRequested() || $controller->actionEditRequested()) {
           $sForm = $oForm->render();
       } else {
           $sForm = "";
       }

       $oView->setData("form", $sForm);
       $oView->setData("titleicon", \Baikal\Core\Icons::bigiconBook());
       $oView->setData("modelicon", \Baikal\Core\Icons::mediumiconUser());
       $oView->setData("modellabel", $oUser->label());
       $oView->setData("linkback", \BaikalAdmin\Controller\Users::link());
       $oView->setData("linknew", $controller->linkNew());
       $oView->setData("addressbookicon", \Baikal\Core\Icons::iconBook());

       return $oView->render();
   }

   /**
    * Delete an AddressBook by ID.
    *
    * @param int $iModel
    * @return bool
    */
   public function delete(int $iModel) {
       try {
           $oModel = new \Baikal\Model\AddressBooks($iModel);
           $oModel->destroy();
       } catch (\Exception $e) {
           // Log the error and return false if the user doesn't exist or is already deleted.
           error_log($e);
       }
   }

}