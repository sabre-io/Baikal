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

use Baikal\Model\User;

class Users extends \BaikalAdmin\Interface\Service {
    /**
     *  Fetches all users and returns them as an array
     * 
     *  @return array
     */
    public function getAll(): array {
        $users = [];
        $userObjects = User::getBaseRequester()->execute();

        foreach ($userObjects as $user) {
            $users[] = [
                "linkcalendars"    => \BaikalAdmin\Controller\Users::linkCalendars($user),
                "linkaddressbooks" => \BaikalAdmin\Controller\Users::linkAddressBooks($user),
                "linkedit"         => \BaikalAdmin\Controller\Users::linkEdit($user),
                "linkdelete"       => \BaikalAdmin\Controller\Users::linkDelete($user),
                "mailtouri"        => $user->getMailtoURI(),
                "username"         => $user->get("username"),
                "displayname"      => $user->get("displayname"),
                "email"            => $user->get("email"),
            ];
        }

        return $users;
    }

    /**
     * Render the view for the users page.
     * 
     * @return string
     */
    public function render(\Baikal\Model\User $oView, 
                            array $aUsers, 
                            array $aMessages, 
                            \Formal\Form $oForm,
                            \BaikalAdmin\Controller\Users $controller
                            ): string {

        $oView->setData("users", $aUsers);
        $oView->setData("calendaricon", \Baikal\Core\Icons::iconCalendar());
        $oView->setData("usericon", \Baikal\Core\Icons::iconUser());
        $oView->setData("davUri", PROJECT_URI . 'dav.php');

        # Messages
        $sMessages = implode("\n", $aMessages);
        $oView->setData("messages", $sMessages);       
        
        # Form
        if ($this->actionNewRequested() || $this->actionEditRequested()) {
            $sForm = $oForm->render();
        } else {
            $sForm = "";
        }
        
        $oView->setData("form", $sForm);
        $oView->setData("usericon", \Baikal\Core\Icons::iconUser());
        $oView->setData("controller", $controller);

        return $oView->render();
    }

    /**
     * Delete a user by ID.
     *
     * @param int $userId
     * @return bool
     */
    public function delete(int $iUser) {
        try {
            $user = new \Baikal\Model\User($iUser);
            $user->destroy();
        } catch (\Exception $e) {
            // Log the error and return false if the user doesn't exist or is already deleted.
            error_log($e);
        }
        # Redirecting to admin home
        \Flake\Util\Tools::redirectUsingMeta($this->link());
    }

}
