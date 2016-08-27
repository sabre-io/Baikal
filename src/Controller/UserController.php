<?php

namespace Baikal\Controller;

use Baikal\Domain\User;
use Baikal\Domain\User\Username;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

class UserController implements ControllerProviderInterface {

    function connect(Application $app) {

        $controllers = $app['controllers_factory'];
        $controllers->get('/', [$this, 'indexAction'])->bind('admin_user_index');

        $controllers->get('/new',        [$this, 'createAction'])->bind('admin_user_create');
        $controllers->post('/new',       [$this, 'postCreateAction'])->bind('admin_user_create_post');
        $controllers->get('{userName}',  [$this, 'editAction'])->bind('admin_user_edit');
        $controllers->post('{userName}', [$this, 'postEditAction'])->bind('admin_user_edit_post');

        $controllers->get('{userName}/delete',  [$this, 'deleteAction'])->bind('admin_user_delete');
        $controllers->post('{userName}/delete',  [$this, 'postDeleteAction'])->bind('admin_user_delete_post');
        $controllers->get('{userName}/calendars', [$this, 'calendarAction'])->bind('admin_user_calendars');
        $controllers->get('{userName}/addressbooks', [$this, 'addressbookAction'])->bind('admin_user_addressbooks');

        return $controllers;
    }

    function indexAction(Application $app) {
        $users = $app['service.user']->all();

        foreach ($users as $user) {
            $principalsUri = $user->getPrincipalUri();
            $user->calendarCount = count($app['sabredav.backend.caldav']->getCalendarsForUser($principalsUri));
            $user->addressbookCount = count($app['sabredav.backend.carddav']->getAddressBooksForUser($principalsUri));
        }

        return $app['twig']->render('admin/user/index.html', [
            'users'    => $users,
            'messages' => '',
            'form'     => '',
        ]);
    }

    function createAction(Application $app, Request $request) {

        if ($request->getMethod() !== Request::METHOD_GET) {
            throw new MethodNotAllowedException([Request::METHOD_GET]);
        }

        return $app['twig']->render('admin/user/create.html', [
            'user' => [
                'username'    => '',
                'displayName' => '',
                'email'       => '',
                'password'    => '',
            ],
        ]);
    }

    function postCreateAction(Application $app, Request $request) {

        if ($request->getMethod() !== Request::METHOD_POST) {
            throw new MethodNotAllowedException([Request::METHOD_POST]);
        }

        $userData = $request->get('data');
        if ($userData['password'] != $userData['passwordconfirm']) {
            throw new \InvalidArgumentException('Passwords did not match');
        }

        $user = User::fromPostForm($userData);
        $app['service.user']->create($user);
        $app['service.calendar']->provision($user);
        $app['service.addressbook']->provision($user);

        return $app->redirect($app['url_generator']->generate('admin_user_index'));
    }

    function editAction(Application $app, $userName) {

        $user = $app['service.user']->getByUsername($userName);

        if ($user === null) {
            $user = [
                'username'    => '',
                'displayName' => '',
                'email'       => '',
                'password'    => '',
            ];
        }

        return $app['twig']->render('admin/user/edit.html', [
            'user' => $user,
        ]);
    }

    function postEditAction(Application $app, Request $request, $userName) {

        $userData = $request->get('data');
        $userData['userName'] = $userName;
        if ($userData['password'] != $userData['passwordconfirm']) {
            throw new \InvalidArgumentException('Passwords did not match');
        }

        $user = User::fromPostForm($userData);
        $app['service.user']->update($user);

        return $app->redirect($app['url_generator']->generate('admin_user_index'));
    }

    function deleteAction(Application $app, $userName) {
        $user = $app['service.user']->getByUsername($userName);

        return $app['twig']->render('admin/user/delete.html', [
            'user' => $user,
        ]);
    }

    function postDeleteAction(Application $app, $userName) {
        $user = $app['service.user']->getByUsername($userName);
        $app['service.user']->remove($user);

        return $app->redirect($app['url_generator']->generate('admin_user_index'));
    }

    function calendarAction(Application $app, $userName) {
        $calendars = $app['sabredav.backend.caldav']->getCalendarsForUser('principals/' . $userName);

        return $app['twig']->render('admin/user/calendars.html', [
            'username'  => $userName,
            'calendars' => $calendars,
        ]);
    }

    function addressbookAction(Application $app, $userName) {
        $addressbooks = $app['sabredav.backend.carddav']->getAddressBooksForUser('principals/' . $userName);

        return $app['twig']->render('admin/user/addressbooks.html', [
            'username'     => $userName,
            'addressbooks' => $addressbooks,
        ]);
    }
}
