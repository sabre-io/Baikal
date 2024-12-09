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

namespace BaikalAdmin\Controller;

class Users extends \Flake\Core\Controller {
    protected $aMessages = [];

    /**
     * @var \Baikal\Model\User
     */
    private $oModel;

    /**
     * @var \Formal\Form
     */
    private $oForm;

    /**
     * @var \BaikalAdmin\Service\Users
     */
    private $uService;

    public function __construct() {
        $uService = new \BaikalAdmin\Service\Users();
    }

    function execute() {
        if ($this->actionEditRequested()) {
            $this->actionEdit();
        }

        if ($this->actionNewRequested()) {
            $this->actionNew();
        }

        if ($this->actionDeleteRequested()) {
            $this->actionDelete();
        }
    }

    function render() {
        $oView = new \BaikalAdmin\View\Users();
        $aUsers = $this->$uService->getAll();
        return $uService->render($oView, $aUsers, $this->aMessages, $this);
    }

    protected function initForm() {
        if ($this->actionEditRequested() || $this->actionNewRequested()) {
            $aOptions = [
                "closeurl" => self::link(),
            ];

            $this->oForm = $this->oModel->formForThisModelInstance($aOptions);
        }
    }

    # Action edit
    protected function actionEditRequested() {
        $aParams = $this->getParams();
        if (array_key_exists("edit", $aParams) && intval($aParams["edit"]) > 0) {
            return true;
        }

        return false;
    }

    protected function actionEdit() {
        $aParams = $this->getParams();
        $this->oModel = new \Baikal\Model\User(intval($aParams["edit"]));
        $this->initForm();

        if ($this->oForm->submitted()) {
            $this->oForm->execute();
        }
    }

    # Action delete

    protected function actionDeleteRequested() {
        $aParams = $this->getParams();
        if (array_key_exists("delete", $aParams) && intval($aParams["delete"]) > 0) {
            return true;
        }

        return false;
    }

    protected function actionDeleteConfirmed() {
        if ($this->actionDeleteRequested() === false) {
            return false;
        }

        $aParams = $this->getParams();

        if (array_key_exists("confirm", $aParams) && intval($aParams["confirm"]) === 1) {
            return true;
        }

        return false;
    }

    protected function actionDelete() {
        $aParams = $this->getParams();
        $iUser = intval($aParams["delete"]);

        if ($this->actionDeleteConfirmed() !== false) {
            # catching Exception thrown when model already destroyed
            # happens when user refreshes delete-page, for instance
            $this->userService->deleteUser($iUser)
            
            # Redirecting to admin home
            \Flake\Util\Tools::redirectUsingMeta($this->link());          
        } else {
            $oUser = new \Baikal\Model\User($iUser);
            $this->aMessages[] = \Formal\Core\Message::warningConfirmMessage(
                "Check twice, you're about to delete " . $oUser->label() . "</strong> from the database !",
                "<p>You are about to delete a user and all it's calendars / contacts. This operation cannot be undone.</p><p>So, now that you know all that, what shall we do ?</p>",
                $this->linkDeleteConfirm($oUser),
                "Delete <strong><i class='" . $oUser->icon() . " icon-white'></i> " . $oUser->label() . "</strong>",
                $this->link()
            );
        }
    }

    # Action new
    protected function actionNewRequested() {
        $aParams = $this->getParams();
        if (array_key_exists("new", $aParams) && intval($aParams["new"]) === 1) {
            return true;
        }

        return false;
    }

    protected function actionNew() {
        $this->oModel = new \Baikal\Model\User();
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

    function linkNew() {
        return self::buildRoute([
            "new" => 1,
        ]) . "#form";
    }

    static function linkEdit(\Baikal\Model\User $user) {
        return self::buildRoute([
            "edit" => $user->get("id"),
        ]) . "#form";
    }

    static function linkDelete(\Baikal\Model\User $user) {
        return self::buildRoute([
            "delete" => $user->get("id"),
        ]) . "#message";
    }

    static function linkDeleteConfirm(\Baikal\Model\User $user) {
        return self::buildRoute([
            "delete"  => $user->get("id"),
            "confirm" => 1,
        ]) . "#message";
    }

    static function linkCalendars(\Baikal\Model\User $user) {
        return \BaikalAdmin\Controller\User\Calendars::buildRoute([
            "user" => $user->get("id"),
        ]);
    }

    static function linkAddressBooks(\Baikal\Model\User $user) {
        return \BaikalAdmin\Controller\User\AddressBooks::buildRoute([
            "user" => $user->get("id"),
        ]);
    }
}
