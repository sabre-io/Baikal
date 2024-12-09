<?php

#################################################################
#  Copyright notice
#
#  (c) 2013 J�r�me Schneider <mail@jeromeschneider.fr>
#  All rights reserved
#
#  http://sabre.io/baikal
#
#  This script is part of the Ba�kal Server project. The Ba�kal
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


You will also need to modify the AddressBooks controller class under BaikalAdmin:

<?php

#################################################################
#  Copyright notice
#
#  (c) 2013 J�r�me Schneider <mail@jeromeschneider.fr>
#  All rights reserved
#
#  http://sabre.io/baikal
#
#  This script is part of the Ba�kal Server project. The Ba�kal
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

namespace BaikalAdmin\Controller\User;

class AddressBooks extends \Flake\Core\Controller {
   protected $aMessages = [];
   protected $oModel;    # \Baikal\Model\Contact
   protected $oUser;    # \Baikal\Model\User
   protected $oForm;    # \Formal\Form

   /**
    * @var \BaikalAdmin\Service\AddressBooks
    */
   private $uService;

   public function __construct() {
       $uService = new \BaikalAdmin\Service\AddressBooks();
   }

   function execute() {
       if (($iUser = $this->currentUserId()) === false) {
           throw new \Exception("BaikalAdmin\Controller\User\Contacts::render(): User get-parameter not found.");
       }

       $this->oUser = new \Baikal\Model\User($iUser);

       if ($this->actionNewRequested()) {
           $this->actionNew();
       }

       if ($this->actionEditRequested()) {
           $this->actionEdit();
       }

       if ($this->actionDeleteRequested()) {
           $this->actionDelete();
       }
   }

   function render() {
       $oView = new \BaikalAdmin\View\User\AddressBooks();
       $aAddressBooks = $this->$uService->getAll();
       return $uService->render($oView, $oUser, $aAddressBooks, $this->aMessages, $oForm, $this);
   }

   protected function initForm() {
       if ($this->actionEditRequested() || $this->actionNewRequested()) {
           $aOptions = [
               "closeurl" => $this->linkHome(),
           ];

           $this->oForm = $this->oModel->formForThisModelInstance($aOptions);
       }
   }

   protected function currentUserId() {
       $aParams = $this->getParams();
       if (($iUser = intval($aParams["user"])) === 0) {
           return false;
       }

       return $iUser;
   }

   # Action new

   function linkNew() {
       return self::buildRoute([
           "user" => $this->currentUserId(),
           "new"  => 1,
       ]) . "#form";
   }

   protected function actionNewRequested() {
       $aParams = $this->getParams();
       if (array_key_exists("new", $aParams) && intval($aParams["new"]) === 1) {
           return true;
       }

       return false;
   }

   protected function actionNew() {
       # Building floating model object
       $this->oModel = new \Baikal\Model\AddressBook();
       $this->oModel->set(
           "principaluri",
           $this->oUser->get("uri")
       );

       $this->initForm();

       if ($this->oForm->submitted()) {
           $this->oForm->execute();

           if ($this->oForm->persisted()) {
               $this->oForm->setOption(
                   "action",
                   $this->linkEdit(
                       $this->oForm->modelInstance()
                   )
               );
           }
       }
   }

   # Action edit

   function linkEdit(\Baikal\Model\AddressBook $oModel) {
       return self::buildRoute([
           "user" => $this->currentUserId(),
           "edit" => $oModel->get("id"),
       ]) . "#form";
   }

   protected function actionEditRequested() {
       $aParams = $this->getParams();
       if (array_key_exists("edit", $aParams) && intval($aParams["edit"]) > 0) {
           return true;
       }

       return false;
   }

   protected function actionEdit() {
       # Building anchored model object
       $aParams = $this->getParams();
       $this->oModel = new \Baikal\Model\AddressBook(intval($aParams["edit"]));

       # Initialize corresponding form
       $this->initForm();

       # Process form
       if ($this->oForm->submitted()) {
           $this->oForm->execute();
       }
   }

   # Action delete + confirm

   function linkDelete(\Baikal\Model\AddressBook $oModel) {
       return self::buildRoute([
           "user"   => $this->currentUserId(),
           "delete" => $oModel->get("id"),
       ]) . "#message";
   }

   function linkDeleteConfirm(\Baikal\Model\AddressBook $oModel) {
       return self::buildRoute([
           "user"    => $this->currentUserId(),
           "delete"  => $oModel->get("id"),
           "confirm" => 1,
       ]) . "#message";
   }

   protected function actionDeleteRequested() {
       $aParams = $this->getParams();
       if (array_key_exists("delete", $aParams) && intval($aParams["delete"]) > 0) {
           return true;
       }

       return false;
   }

   protected function actionDeleteConfirmed() {
       if (($iPrimary = $this->actionDeleteRequested()) === false) {
           return false;
       }

       $aParams = $this->getParams();
       if (array_key_exists("confirm", $aParams) && intval($aParams["confirm"]) > 0) {
           return true;
       }

       return false;
   }

   protected function actionDelete() {
       $aParams = $this->getParams();
       $iModel = intval($aParams["delete"]);

       if ($this->actionDeleteConfirmed() !== false) {
           # catching Exception thrown when model already destroyed
           # happens when user refreshes page on delete-URL, for instance
           $this->$uService->delete($iModel);

           # Redirecting to admin home
           \Flake\Util\Tools::redirectUsingMeta($this->linkHome());
       } else {
           $oModel = new \Baikal\Model\AddressBook($iModel);
           $this->aMessages[] = \Formal\Core\Message::warningConfirmMessage(
               "Check twice, you're about to delete " . $oModel->label() . "</strong> from the database !",
               "<p>You are about to delete a contact book and all it's visiting cards. This operation cannot be undone.</p><p>So, now that you know all that, what shall we do ?</p>",
               $this->linkDeleteConfirm($oModel),
               "Delete <strong><i class='" . $oModel->icon() . " icon-white'></i> " . $oModel->label() . "</strong>",
               $this->linkHome()
           );
       }
   }

   # Link to home
   function linkHome() {
       return self::buildRoute([
           "user" => $this->currentUserId(),
       ]);
   }

   /**
    * Generate a link to the CalDAV/CardDAV URI of the addressbook.
    *
    * @param \Baikal\Model\AddressBook $addressbook
    *
    * @return string AddressBook DAV URI
    */
   protected function getDavUri(\Baikal\Model\AddressBook $addressbook) {
       return PROJECT_URI . 'dav.php/addressbooks/' . $this->oUser->get('username') . '/' . $addressbook->get('uri') . '/';
   }
}