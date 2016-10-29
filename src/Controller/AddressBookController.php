<?php

namespace Baikal\Controller;

use Baikal\Domain\User;
use Baikal\Domain\User\Username;
use Sabre\DAV\PropPatch;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class AddressBookController {

    function indexAction(Application $app, User $user) {

        $addressbooks = $app['sabredav.backend.carddav']->getAddressBooksForUser('principals/' . $user->userName);
        $addressbooksData = [];

        foreach ($addressbooks as $addressbook) {
            $addressbookId = $addressbook['id'];
            $addressbook['cardCount'] = count($app['sabredav.backend.carddav']->getCards($addressbookId));
            $addressbooksData[] = $addressbook;
        }
        return $app['twig']->render('admin/addressbook/index.html', [
            'user'         => $user,
            'addressbooks' => $addressbooksData,
        ]);
    }

    function createAction(Application $app, Request $request, User $user) {

        if ($request->getMethod() !== Request::METHOD_GET) {
            throw new MethodNotAllowedException([Request::METHOD_GET]);
        }

        return $app['twig']->render('admin/addressbook/create.html', [
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

        $addressbookData = $request->get('data');

        $app['service.addressbook']->createAddressBook($user, $addressbookData['displayName'], $addressbookData['addressbookDescription']);

        return $app->redirect($app['url_generator']->generate('admin_user_addressbooks', ['user' => $user->userName]));
    }

    function editAction(Application $app, User $user, $addressbookId) {

        $addressbook = $app['service.addressbook']->getByUserNameAndAddressBookId($user->userName, $addressbookId);

        return $app['twig']->render('admin/addressbook/edit.html', [
            'user'        => $user,
            'addressbook' => $addressbook
        ]);
    }

    function postEditAction(Application $app, Request $request, User $user, $addressbookId) {

        $proppatch = new PropPatch([
            '{DAV:}displayname'                                       => $request->get('data')['displayname'],
            '{urn:ietf:params:xml:ns:carddav}addressbook-description' => $request->get('data')['description']
        ]);
        $addressbook = $app['sabredav.backend.carddav']->updateAddressBook(
            $addressbookId,
            $proppatch
        );
        $proppatch->commit();
        return $app->redirect($app['url_generator']->generate('admin_user_addressbooks', ['user' => $user->userName]));

    }

    function deleteAction(Application $app, User $user, $addressbookId) {

        $addressbook = $app['service.addressbook']->getByUserNameAndAddressBookId($user->userName, $addressbookId);

        return $app['twig']->render('admin/addressbook/delete.html', [
            'user'        => $user,
            'addressbook' => $addressbook
        ]);
    }

    function postDeleteAction(Application $app, User $user, $addressbookId) {

        $app['sabredav.backend.carddav']->deleteAddressbook($addressbookId);
        return $app->redirect($app['url_generator']->generate('admin_user_addressbooks', ['user' => $user->userName]));

    }

}
