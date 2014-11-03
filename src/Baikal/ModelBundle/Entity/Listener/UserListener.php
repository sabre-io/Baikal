<?php

namespace Baikal\ModelBundle\Entity\Listener;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Doctrine\ORM\Event\LifecycleEventArgs;

use Baikal\ModelBundle\Entity\User,
    Baikal\ModelBundle\Entity\Repository\UserRepository,
    Baikal\ModelBundle\Entity\Repository\CalendarRepository,
    Baikal\ModelBundle\Entity\Repository\AddressbookRepository;

class UserListener {

    public function __construct(
        CalendarRepository $calendarRepository,
        AddressbookRepository $addressbookRepository
    ) {
        $this->calendarRepository = $calendarRepository;
        $this->addressbookRepository = $addressbookRepository;
    }

    public function postLoad(User $user, LifecycleEventArgs $event) {
        # Loading the principals/calendars/addressbooks for a user in the User postLoad doctrine event
        # As doctrine can't handle relationships natively on non-primarykey values

        $em = $event->getObjectManager();

        #var_dump($em->getRepository('\Baikal\ModelBundle\Entity\UserPrincipal')->findByUser($user));

        $user->setPrincipals(
            $em->getRepository('\Baikal\ModelBundle\Entity\UserPrincipal')->findByUser($user)
        );
        
        $user->setCalendars(
            $this->calendarRepository->findByUser($user)
        );
        
        $user->setAddressbooks(
            $this->addressbookRepository->findByUser($user)
        );
    }

    public function preRemove(User $user, LifecycleEventArgs $event) {

        # Removing the metadata/principals/calendars/addressbooks for a user in the User preRemove doctrine event
        # As doctrine can't handle relationships natively on non-primarykey values

        $em = $event->getObjectManager();

        foreach($user->getPrincipals() as $principal) {
            $em->remove($principal);
        }

        foreach($user->getCalendars() as $calendar) {
            $em->remove($calendar);
        }

        foreach($user->getAddressbooks() as $addressbook) {
            $em->remove($addressbook);
        }

        $em->flush();
    }
}