<?php

namespace Baikal\BootCampBundle\InitHandler;

use Doctrine\ORM\EntityManager;

use Symfony\BootCampBundle\InitHandler\UserInitHandlerInterface;

use Baikal\SystemBundle\Entity\User,
    Baikal\ModelBundle\Entity\UserPrincipal;

class UserInitHandler implements UserInitHandlerInterface {

    protected $entityManager;
    protected $passwordencoder_factory;

    public function __construct(
        EntityManager $entityManager,
        $passwordencoder
    ) {
        $this->entityManager = $entityManager;
        $this->passwordencoder = $passwordencoder;
    }

    public function createAndPersistUser($username, $password) {
        
        # Persisting identity principal
        $principalidentity = new UserPrincipal();
        $principalidentity->setDisplayname(ucwords($username));
        $principalidentity->setUri('principals/' . $username);
        $principalidentity->setEmail('admin@example.com');

        $this->entityManager->persist($principalidentity);

        # Persisting user
        $user = new User();
        $user->setUsername($username);  # Not setting salt; handled by the user entity
        $user->setPassword(
            $this->passwordencoder->encodePassword(
                $password,
                $user->getSalt()
            )
        );

        $user->addRole('ROLE_ADMIN');
        $user->addRole('ROLE_FRONTEND_USER');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}