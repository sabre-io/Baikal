<?php

namespace Baikal\Controller\Admin;

use Baikal\Domain\User;
use Baikal\Domain\User\Username;
use Baikal\Framework\Silex\Controller;
use Baikal\Domain\UserRepository;
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

    function __construct(Twig_Environment $twig, UrlGeneratorInterface $urlGenerator, UserRepository $userRepository)
    {
        parent::__construct($twig, $urlGenerator);
        $this->userRepository = $userRepository;
    }

    function indexAction()
    {
        $users = $this->userRepository->all();

        return $this->render('Admin/users', [
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

        return $this->render('Admin/users_form', [
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

        $user = User::fromArray($request->get('data'));
        $this->userRepository->persist($user);

        return $this->redirect('admin_users');
    }

    function editAction($username)
    {
        $username = Username::fromString($username);
        $user = $this->userRepository->getByUsername($username);

        if ($user === null) {
            $user = [
                'username'    => '',
                'displayName' => '',
                'email'       => '',
                'password'    => '',
            ];
        }

        return $this->render('Admin/users_form', [
            'user' => $user,
        ]);
    }

    function deleteAction($username)
    {
        $username = Username::fromString($username);
        $user = $this->userRepository->getByUsername($username);

        if ($user === null) {
            return $this->redirect('admin_users');
        }

        return $this->render('Admin/user/delete', [
            'username' => $username,
        ]);
    }

    function postDeleteAction($username)
    {
        $username = Username::fromString($username);
        $user = $this->userRepository->getByUsername($username);

        if ($user === null) {
            return $this->redirect('admin_users');
        }

        $this->userRepository->remove($user);

        return $this->redirect('admin_users');
    }

    function calendarAction($username)
    {
        return $this->render('Admin/user/calendars', [
            'username'  => $username,
            'calendars' => [],
        ]);
    }

    function addressbookAction($username)
    {
        return $this->render('Admin/user/addressbooks', [
            'username'     => $username,
            'addressbooks' => [],
        ]);
    }
}
