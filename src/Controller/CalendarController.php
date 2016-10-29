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

    function createAction(Application $app, Request $request, User $user) {

        if ($request->getMethod() !== Request::METHOD_GET) {
            throw new MethodNotAllowedException([Request::METHOD_GET]);
        }

        return $app['twig']->render('admin/calendar/create.html', [
            'user'         => $user,
//            'calendar' => [
//                  'displayName' => '',
//                  'calendarDescription' => '',
//            ],
        ]);
    }

    function postCreateAction(Application $app, Request $request, User $user) {

        if ($request->getMethod() !== Request::METHOD_POST) {
            throw new MethodNotAllowedException([Request::METHOD_POST]);
        }

        $calendarData = $request->get('data');

        $app['service.calendar']->createCalendar($user, $calendarData['displayName'], $calendarData['calendarDescription']);

        return $app->redirect($app['url_generator']->generate('admin_user_calendars', ['user' => $user->userName]));
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
            '{DAV:}displayname'                                   => $request->get('data')['displayName'],
            '{urn:ietf:params:xml:ns:caldav}calendar-description' => $request->get('data')['calendarDescription']
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
