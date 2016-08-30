<?php

namespace Baikal\Controller;

use Baikal\Domain\User;
use Baikal\Domain\User\Username;
use Silex\Application;

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
