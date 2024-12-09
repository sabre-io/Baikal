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

use Baikal\Model\Calendar;

class Calendars extends \BaikalAdmin\Service\Service {
    /**
     *  Fetches all calendars and returns them as an array
     * 
     *  @return array
     */
    public function getAll(): array {
        # List of calendars
        $oCalendars = \Baikal\Model\User::getCalendarsBaseRequester()->execute();
        $aCalendars = [];

        foreach ($oCalendars as $calendar) {
            $aCalendars[] = [
                "linkedit"    => \BaikalAdmin\Controller\User\Calendars::->linkEdit($calendar),
                "linkdelete"  => \BaikalAdmin\Controller\User\Calendars::->linkDelete($calendar),
                "davuri"      => \BaikalAdmin\Controller\User\Calendars::->getDavUri($calendar),
                "icon"        => $calendar->icon(),
                "label"       => $calendar->label(),
                "instanced"   => $calendar->hasInstances(),
                "events"      => $calendar->getEventsBaseRequester()->count(),
                "description" => $calendar->get("description"),
            ];
        }

        return $aCalendars;
    }

    /**
     * Render the view for the calendars page.
     * 
     * @return string
     */
    public function render(\BaikalAdmin\View\User\Calendars $oView
                            \Baikal\Model\User $oUser, 
                            array $aCalendars, 
                            array $aMessages, 
                            \Formal\Form $oForm,
                            \BaikalAdmin\Controller\User\Calendars $controller
                            ): string {

        # User
        $oView->setData("user", $this->oUser);

        $oView->setData("calendars", $aCalendars);

        # Messages
        $sMessages = implode("\n", $aMessages);
        $oView->setData("messages", $sMessages);

        if ($controller->actionNewRequested() || $controller->actionEditRequested()) {
            $sForm = $oForm->render();
        } else {
            $sForm = "";
        }

        $oView->setData("form", $sForm);
        $oView->setData("titleicon", \Baikal\Core\Icons::bigiconCalendar());
        $oView->setData("modelicon", \Baikal\Core\Icons::mediumiconUser());
        $oView->setData("modellabel", $oUser->label());
        $oView->setData("linkback", \BaikalAdmin\Controller\Users::link());
        $oView->setData("linknew", $controller->linkNew());
        $oView->setData("calendaricon", \Baikal\Core\Icons::iconCalendar());

        return $oView->render();
    }

    /**
     * Delete a calendar by ID.
     *
     * @param int $iCalendar
     * @return bool
     */
    public function delete(int $iCalendar) {
        try {
            $calendar = new \Baikal\Model\Calendar($iCalendar);
            $calendar->destroy();
        } catch (\Exception $e) {
            // Log the error and return false if the user doesn't exist or is already deleted.
            error_log($e);
        }
    }

}