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

namespace BaikalAdmin\Controller\User;

use Symfony\Component\Yaml\Yaml;

class Calendars extends \Flake\Core\Controller {
    protected $aMessages = [];
    protected $bBirthdayCalendarsAvailable;  # read from baikal.yaml once per request
    protected $oModel;    # \Baikal\Model\Calendar
    protected $oUser;    # \Baikal\Model\User
    protected $oForm;    # \Formal\Form

    function execute() {
        if (($iUser = $this->currentUserId()) === false) {
            throw new \Exception("BaikalAdmin\Controller\User\Calendars::render(): User get-parameter not found.");
        }

        $this->oUser = new \Baikal\Model\User($iUser);

        if ($this->actionNewRequested()) {
            $this->actionNew();
        } elseif ($this->actionEditRequested()) {
            $this->actionEdit();
        } elseif ($this->actionDeleteRequested()) {
            $this->actionDelete();
        } elseif ($this->actionBirthdaysRequested()) {
            $this->actionBirthdays();
        }
    }

    function render() {
        $oView = new \BaikalAdmin\View\User\Calendars();

        # User
        $oView->setData("user", $this->oUser);

        # List of calendars
        $oCalendars = $this->oUser->getCalendarsBaseRequester()->execute();
        $aCalendars = [];

        $bHasBirthdayCalendar = false;

        foreach ($oCalendars as $calendar) {
            $bBirthdays = $calendar->isBirthdayCalendar();
            $bHasBirthdayCalendar = $bHasBirthdayCalendar || $bBirthdays;

            $aCalendars[] = [
                "linkedit"    => $this->linkEdit($calendar),
                "linkdelete"  => $this->linkDelete($calendar),
                "davuri"      => $this->getDavUri($calendar),
                "icon"        => $calendar->icon(),
                "label"       => $calendar->label(),
                "instanced"   => $calendar->hasInstances(),
                "events"      => $calendar->getEventsBaseRequester()->count(),
                "description" => $calendar->get("description"),
                "birthdays"   => $bBirthdays,
                "linkrebuild" => $bBirthdays ? $this->linkBirthdays("rebuild") : "",
            ];
        }

        $oView->setData("calendars", $aCalendars);
        $oView->setData(
            "linknewbirthdays",
            (!$bHasBirthdayCalendar && $this->birthdayCalendarsAvailable()) ? $this->linkBirthdays("create") : ""
        );
        # The birthday actions are sent as POST, which is checked against the CSRF token
        $oView->setData("csrftoken", $_SESSION["CSRF_TOKEN"] ?? "");

        # Messages
        $sMessages = implode("\n", $this->aMessages);
        $oView->setData("messages", $sMessages);

        if ($this->actionNewRequested() || $this->actionEditRequested()) {
            $sForm = $this->oForm->render();
        } else {
            $sForm = "";
        }

        $oView->setData("form", $sForm);
        $oView->setData("titleicon", \Baikal\Model\Calendar::bigicon());
        $oView->setData("modelicon", $this->oUser->mediumicon());
        $oView->setData("modellabel", $this->oUser->label());
        $oView->setData("linkback", \BaikalAdmin\Controller\Users::link());
        $oView->setData("linknew", $this->linkNew());
        $oView->setData("calendaricon", \Baikal\Model\Calendar::icon());

        return $oView->render();
    }

    protected function initForm() {
        if ($this->actionEditRequested() || $this->actionNewRequested()) {
            $aOptions = [
                "closeurl"        => $this->linkHome(),
                "hook.validation" => [$this, "validateReservedUri"],
            ];

            $this->oForm = $this->oModel->formForThisModelInstance($aOptions);
        }
    }

    # The URI of birthday calendars may not be used for other calendars
    function validateReservedUri(\Formal\Form $oForm, \Formal\Form\Morphology $oMorpho) {
        if ($oForm->floatingModelInstance() && $oMorpho->element("uri")->value() === \Baikal\Core\BirthdayCalendar\BirthdayService::CALENDAR_URI) {
            $oForm->declareError(
                $oMorpho->element("uri"),
                "<strong>" . $oMorpho->element("uri")->option("label") . "</strong> '" . \Baikal\Core\BirthdayCalendar\BirthdayService::CALENDAR_URI . "' is reserved for the birthday calendar."
            );
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
        $this->oModel = new \Baikal\Model\Calendar();
        $this->oModel->set(
            "principaluri",
            $this->oUser->get("uri")
        );

        $this->oModel->set(
            "components",
            "VEVENT"
        );

        # Initialize corresponding form
        $this->initForm();

        # Process form
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

    function linkEdit(\Baikal\Model\Calendar $oModel) {
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
        $this->oModel = new \Baikal\Model\Calendar(intval($aParams["edit"]));

        # Initialize corresponding form
        $this->initForm();

        # Process form
        if ($this->oForm->submitted()) {
            $this->oForm->execute();
        }
    }

    # Action delete + confirm

    function linkDelete(\Baikal\Model\Calendar $oModel) {
        return self::buildRoute([
            "user"   => $this->currentUserId(),
            "delete" => $oModel->get("id"),
        ]) . "#message";
    }

    function linkDeleteConfirm(\Baikal\Model\Calendar $oModel) {
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
        $iCalendar = intval($aParams["delete"]);

        if ($this->actionDeleteConfirmed() !== false) {
            # catching Exception thrown when model already destroyed
            # happens when user refreshes page on delete-URL, for instance

            try {
                $oModel = new \Baikal\Model\Calendar($iCalendar);
                $oModel->destroy();
            } catch (\Exception $e) {
                # already deleted; silently discarding
            }

            # Redirecting to admin home
            \Flake\Util\Tools::redirectUsingMeta($this->linkHome());
        } else {
            $oModel = new \Baikal\Model\Calendar($iCalendar);
            $this->aMessages[] = \Formal\Core\Message::warningConfirmMessage(
                "Check twice, you're about to delete " . htmlspecialchars($oModel->label()) . "</strong> from the database !",
                "<p>You are about to delete a calendar and all it's scheduled events. This operation cannot be undone.</p><p>So, now that you know all that, what shall we do ?</p>",
                $this->linkDeleteConfirm($oModel),
                "Delete <strong><i class='" . $oModel->icon() . " icon-white'></i> " . htmlspecialchars($oModel->label()) . "</strong>",
                $this->linkHome()
            );
        }
    }

    # Action birthdays (create or rebuild the birthday calendar)

    function linkBirthdays($sAction) {
        return self::buildRoute([
            "user"      => $this->currentUserId(),
            "birthdays" => $sAction,
        ]) . "#message";
    }

    protected function actionBirthdaysRequested() {
        $aParams = $this->getParams();

        # Only POST requests are checked against the CSRF token
        return ($_SERVER["REQUEST_METHOD"] ?? "") === "POST"
            && array_key_exists("birthdays", $aParams)
            && in_array($aParams["birthdays"], ["create", "rebuild"], true);
    }

    protected function actionBirthdays() {
        $aParams = $this->getParams();
        $sPrincipalUri = $this->oUser->get("uri");

        if (!$this->birthdayCalendarsAvailable()) {
            $this->aMessages[] = \Formal\Core\Message::error(
                "Birthday calendars require both CalDAV and CardDAV to be enabled."
            );

            return;
        }

        try {
            $oBirthdayService = new \Baikal\Core\BirthdayCalendar\BirthdayService($GLOBALS["DB"]->getPDO());
            if ($aParams["birthdays"] === "create") {
                $oBirthdayService->ensureCalendarExists($sPrincipalUri);
            }
            $oBirthdayService->syncPrincipal($sPrincipalUri);
        } catch (\Exception $e) {
            $this->aMessages[] = \Formal\Core\Message::error(htmlspecialchars($e->getMessage()));

            return;
        }

        # Redirecting to admin home
        \Flake\Util\Tools::redirectUsingMeta($this->linkHome());
    }

    # Birthday calendars can only be created and rebuilt if CalDAV and CardDAV are enabled.
    # Like cal.php and card.php, only the value true counts as enabled.
    protected function birthdayCalendarsAvailable() {
        if ($this->bBirthdayCalendarsAvailable === null) {
            try {
                $config = Yaml::parseFile(PROJECT_PATH_CONFIG . "baikal.yaml");
                $this->bBirthdayCalendarsAvailable = ($config['system']["cal_enabled"] ?? false) === true
                    && ($config['system']["card_enabled"] ?? false) === true;
            } catch (\Exception $e) {
                error_log('Error reading baikal.yaml file : ' . $e->getMessage());
                $this->bBirthdayCalendarsAvailable = false;
            }
        }

        return $this->bBirthdayCalendarsAvailable;
    }

    # Link to home

    function linkHome() {
        return self::buildRoute([
            "user" => $this->currentUserId(),
        ]);
    }

    /**
     * Generate a link to the CalDAV/CardDAV URI of the calendar.
     *
     * @param \Baikal\Model\Calendar $calendar
     *
     * @return string Calender DAV URI
     */
    protected function getDavUri(\Baikal\Model\Calendar $calendar) {
        return PROJECT_URI . 'dav.php/calendars/' . $this->oUser->get('username') . '/' . $calendar->get('uri') . '/';
    }
}
