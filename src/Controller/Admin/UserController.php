<?php

namespace Baikal\Controller\Admin;

use Baikal\Controller\Controller;
use Baikal\Domain\User;
use Baikal\Domain\User\Username;
use Baikal\Repository\CalendarRepository;
use Baikal\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig_Environment;

final class UserController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var CalendarRepository
     */
    private $calendarRepository;

    function __construct(Twig_Environment $twig, UrlGeneratorInterface $urlGenerator, UserRepository $userRepository, CalendarRepository $calendarRepository)
    {
        parent::__construct($twig, $urlGenerator);
        $this->userRepository = $userRepository;
        $this->calendarRepository = $calendarRepository;
    }

    function indexAction()
    {
        $users = $this->userRepository->all();

        return $this->render('admin/user/index', [
            'users'    => $users,
            'messages' => '',
            'form'     => '',
        ]);
    }

    function createAction(Request $request)
    {
        if ($request->getMethod() !== Request::METHOD_GET) {
            throw new MethodNotAllowedException([Request::METHOD_GET]);
        }

        return $this->render('admin/user/create', [
            'user' => [
                'username'    => '',
                'displayName' => '',
                'email'       => '',
                'password'    => '',
            ],
        ]);
    }

    function postCreateAction(Request $request)
    {
        if ($request->getMethod() !== Request::METHOD_POST) {
            throw new MethodNotAllowedException([Request::METHOD_POST]);
        }

        $userData = $request->get('data');
        if ($userData['password'] != $userData['passwordconfirm']) {
            throw new \InvalidArgumentException('Passwords did not match');
        }

        $user = User::fromPostForm($userData);
        $this->userRepository->create($user);

        return $this->redirect('admin_user_index');
    }

    function editAction($userName)
    {
        $user = $this->userRepository->getByUsername($userName);

        if ($user === null) {
            $user = [
                'username'    => '',
                'displayName' => '',
                'email'       => '',
                'password'    => '',
            ];
        }

        return $this->render('admin/user/edit', [
            'user' => $user,
        ]);
    }

    function postEditAction($userName, Request $request)
    {
        if ($request->getMethod() !== Request::METHOD_POST) {
            throw new MethodNotAllowedException([Request::METHOD_POST]);
        }

        $userData = $request->get('data');
        $userData['userName'] = $userName;
        if ($userData['password'] != $userData['passwordconfirm']) {
            throw new \InvalidArgumentException('Passwords did not match');
        }

        $user = User::fromPostForm($userData);
        $this->userRepository->update($user);

        return $this->redirect('admin_user_index');
    }

    function deleteAction($userName)
    {
        $user = $this->userRepository->getByUsername($userName);

        if ($user === null) {
            return $this->redirect('admin_user_index');
        }

        return $this->render('admin/user/delete', [
            'user' => $user,
        ]);
    }

    function postDeleteAction($userName)
    {
        $user = $this->userRepository->getByUsername($userName);

        if ($user === null) {
            return $this->redirect('admin_user_index');
        }

        $this->userRepository->remove($user);

        return $this->redirect('admin_user_index');
    }

    function calendarAction($userName)
    {
        $calendars = $this->calendarRepository->allCalendarsByUserName($userName);

        #return json_encode($calendars);
        return $this->render('admin/user/calendars', [
            'username'  => $userName,
            'calendars' => $calendars,
        ]);
    }

    function addressbookAction($userName)
    {
        return $this->render('admin/user/addressbooks', [
            'username'     => $userName,
            'addressbooks' => [],
        ]);
    }
}
