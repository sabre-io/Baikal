<?php

namespace Baikal\Controller;

use Baikal\Domain\User;
use Baikal\Domain\User\Username;
use Sabre\DAV\PropPatch;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class CalendarController {

    function indexAction(Application $app, User $user) {

        $calendars = $app['sabredav.backend.caldav']->getCalendarsForUser('principals/' . $user->userName);
        $calendarsData = [];

        foreach ($calendars as $calendar) {
            $calendarId = $calendar['id'];
            $calendar['eventCount'] = count($app['sabredav.backend.caldav']->getCalendarObjects($calendarId));
            $calendarsData[] = $calendar;
        }
        return $app['twig']->render('admin/calendar/index.html', [
            'user'      => $user,
            'calendars' => $calendarsData,
        ]);
    }

    function editAction(Application $app, User $user, $calendarId) {

        $calendar = $app['service.calendar']->getByUserNameAndCalendarId($user->userName, $calendarId);

        return $app['twig']->render('admin/calendar/edit.html', [
            'user'     => $user,
            'calendar' => $calendar,
        ]);
    }

    function postEditAction(Application $app, Request $request, User $user, $calendarId) {

        $proppatch = new PropPatch([
            '{DAV:}displayname'                                   => $request->get('data')['displayname'],
            '{urn:ietf:params:xml:ns:caldav}calendar-description' => $request->get('data')['description']
        ]);
        $calendar = $app['sabredav.backend.caldav']->updateCalendar(
            $calendarId,
            $proppatch
        );
        $proppatch->commit();
        return $app->redirect($app['url_generator']->generate('admin_user_calendars', ['user' => $user->userName]));

    }

    function deleteAction(Application $app, User $user, $calendarId) {

        $calendar = $app['service.calendar']->getByUserNameAndCalendarId($user->userName, $calendarId);

        return $app['twig']->render('admin/calendar/delete.html', [
            'user'     => $user,
            'calendar' => $calendar
        ]);
    }

    function postDeleteAction(Application $app, User $user, $calendarId) {

        $app['sabredav.backend.caldav']->deleteCalendar($calendarId);
        return $app->redirect($app['url_generator']->generate('admin_user_calendars', ['user' => $user->userName]));

    }

}
