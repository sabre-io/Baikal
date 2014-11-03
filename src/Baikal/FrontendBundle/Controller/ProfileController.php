<?php

namespace Baikal\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response;

use Baikal\FrontendBundle\Form\Type as FormType;

class ProfileController extends Controller {
    
    public function indexAction(Request $request) {

        $user = $this->get('security.context')->getToken()->getUser();
        $principalidentity = $user->getIdentityPrincipal();

        $data = array(
            'displayname' => $principalidentity->getDisplayname(),
            'email' => $principalidentity->getEmail(),
            'roles' => $user->getRoles(),
        );

        $form = $this->get('form.factory')->create(new FormType\User\EditUserType());

        $form->setData($data);
        $form->handleRequest($request);

        if($form->isValid()) {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();

            # Persisting identity principal
            $principalidentity->setDisplayname($data['displayname']);
            $principalidentity->setEmail($data['email']);

            $em->persist($principalidentity);

            # Persisting user if password changed
            if(!is_null($data['password'])) {
                $password = $data['password'];

                $user->setPassword(
                    $this->get('security.encoder_factory')
                        ->getEncoder($user)
                        ->encodePassword(
                            $password,
                            $user->getSalt()
                        )
                );
            }

             # Persisting user roles
            $user->setRoles($data['roles']);

            $em->persist($user);
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', 'User <i class="fa fa-user"></i> <strong>' . htmlspecialchars($user->getUsername()) . '</strong> has been updated.');
            return $this->redirect($this->generateUrl('baikal_admin_user_list'));
        }

        return $this->render('BaikalFrontendBundle:Profile:index.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }
}
