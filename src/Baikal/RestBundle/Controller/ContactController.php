<?php

namespace Baikal\RestBundle\Controller;

use Symfony\Component\Security\Core\SecurityContextInterface,
    Symfony\Component\HttpKernel\Exception\HttpException;

use FOS\RestBundle\View\View,
    FOS\RestBundle\View\ViewHandlerInterface;

use Sabre\VObject;

use Baikal\ModelBundle\Entity\Repository\AddressbookContactRepository,
    Baikal\ModelBundle\Entity\Addressbook,
    Baikal\ModelBundle\Entity\AddressbookContact,
    Baikal\CoreBundle\Services\MainConfigService;

class ContactController {

    protected $viewhandler;
    protected $securityContext;
    protected $contactRepo;
    protected $mainconfig;

    public function __construct(
        ViewHandlerInterface $viewhandler,
        SecurityContextInterface $securityContext,
        AddressbookContactRepository $contactRepo,
        MainConfigService $mainconfig
    ) {
        $this->viewhandler = $viewhandler;
        $this->securityContext = $securityContext;
        $this->contactRepo = $contactRepo;
        $this->mainconfig = $mainconfig;
    }
    
    public function getContactsAction(Addressbook $addressbook) {

        if(!$this->securityContext->isGranted('rest.api')) {
            throw new HttpException(401, 'Unauthorized access.');
        }

        $contacts = $this->contactRepo->findByAddressbook($addressbook);

        return $this->viewhandler->handle(
            View::create([
                'contact' => $contacts,
                'meta' => [
                    'total' => count($contacts),
                ]
            ], 200)
        );
    }

    public function getContactAction(Addressbook $addressbook, AddressbookContact $contact) {

        if(!$this->securityContext->isGranted('dav.read', $addressbook)) {
            throw new HttpException(401, 'Unauthorized access.');
        }

        return $this->viewhandler->handle(
            View::create([
                'contact' => $contact,
            ], 200)
        );
    }
}
