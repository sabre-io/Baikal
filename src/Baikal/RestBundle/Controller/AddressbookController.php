<?php

namespace Baikal\RestBundle\Controller;

# Rest
use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpKernel\Exception\HttpException,
    Symfony\Component\Security\Core\SecurityContextInterface,
    Doctrine\ORM\EntityManagerInterface,
    Symfony\Bundle\FrameworkBundle\Routing\Router,
    Symfony\Component\Form\FormFactoryInterface,
    FOS\RestBundle\View\View,
    FOS\RestBundle\View\ViewHandlerInterface,
    FOS\RestBundle\Controller\Annotations\QueryParam;

use Sabre\VObject;

use Baikal\ModelBundle\Entity\Repository\AddressbookRepository,
    Baikal\ModelBundle\Entity\Addressbook,
    Baikal\ModelBundle\Form\Type\Addressbook\AddressbookType,
    Baikal\CoreBundle\Services\MainConfigService;

class AddressbookController {

    protected $em;
    protected $router;
    protected $formFactory;
    protected $viewhandler;
    protected $securityContext;
    protected $addressbookRepo;
    protected $mainconfig;

    public function __construct(
        EntityManagerInterface $em,
        Router $router,
        FormFactoryInterface $formFactory,
        ViewHandlerInterface $viewhandler,
        SecurityContextInterface $securityContext,
        AddressbookRepository $addressbookRepo,
        MainConfigService $mainconfig
    ) {
        $this->em = $em;
        $this->router = $router;
        $this->formFactory = $formFactory;
        $this->viewhandler = $viewhandler;
        $this->securityContext = $securityContext;
        $this->addressbookRepo = $addressbookRepo;
        $this->mainconfig = $mainconfig;
    }
    
    public function getAddressbooksAction() {

        if(!$this->securityContext->isGranted('rest.api')) {
            throw new HttpException(401, 'Unauthorized access.');
        }

        if($this->securityContext->isGranted('ROLE_ADMIN')) {
            $addressbooks = $this->addressbookRepo->findAll();
        } else {
            $addressbooks = $this->addressbookRepo->findByUser(
                $this->securityContext->getToken()->getUser()
            );
        }

        return $this->viewhandler->handle(
            View::create([
                'addressbook' => $addressbooks,
                'meta' => [
                    'total' => count($addressbooks),
                ]
            ], 200)
        );
    }

    public function getAddressbookAction(Addressbook $addressbook) {

        if(!$this->securityContext->isGranted('dav.read', $addressbook)) {
            throw new HttpException(401, 'Unauthorized access.');
        }

        return $this->viewhandler->handle(
            View::create($addressbook, 200)
        );
    }

    public function putAddressbookAction(Request $request, Addressbook $addressbook) {

        throw new HttpException(501, 'Not implemented.');

        if(!$this->securityContext->isGranted('dav.write', $addressbook)) {
            throw new HttpException(401, 'Unauthorized access.');
        }

        $data = json_decode($request->getContent(), TRUE);
        unset($data['id']);

        $form = $this->formFactory->create(new AddressbookType(), $addressbook);        
        $form->submit($data);

        if(!$form->isValid()) {
            return $this->viewhandler->handle(
                View::create($form->getErrors(), Response::HTTP_BAD_REQUEST)
            );
        }

        $this->em->persist($addressbook);
        $this->em->flush();

        return Response::create()->setStatusCode(Response::HTTP_ACCEPTED);
    }

    protected function updateFromDTO(Addressbook &$addressbook, $data) {
        if(array_key_exists('displayname', $data)) {
            $addressbook->setDisplayname($data['displayname']);
        }

        if(array_key_exists('description', $data)) {
            $addressbook->setDescription($data['description']);
        }
    }
}
