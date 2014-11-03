<?php

namespace Baikal\AdminBundle\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response;

use Baikal\ModelBundle\Entity\User,
    Baikal\ModelBundle\Entity\UserPrincipal,
    Baikal\ModelBundle\Entity\UserMetadata,
    Baikal\ModelBundle\Form\Type as FormType;

class FormController extends Controller
{
    public function newAction(Request $request) {

        $form = $this->get('form.factory')->create(new FormType\User\CreateUserType());

        $form->handleRequest($request);

        if($form->isValid()) {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();

            # Persisting identity principal
            $principalidentity = new UserPrincipal();
            $principalidentity->setDisplayname($data['displayname']);
            $principalidentity->setUri('principals/' . $data['username']);
            $principalidentity->setEmail($data['email']);

            $em->persist($principalidentity);

            # Persisting user
            $user = new User();
            $user->setUsername($data['username']);
            $user->setPassword(
                $this->get('security.encoder_factory')
                    ->getEncoder($user)
                    ->encodePassword(
                        $data['password'],
                        $user->getSalt()
                    )
            );

            $em->persist($user);

            # Persisting user metadata
            $metadata = new UserMetadata();
            $metadata->setUser($user);

            foreach($data['roles'] as $role) {
                $metadata->addRole($role);
            }

            $em->persist($metadata);

            $em->flush();
            $this->get('session')->getFlashBag()->add('notice', 'User <i class="fa fa-user"></i> <strong>' . htmlspecialchars($user->getUsername()) . '</strong> has been created.');
            return $this->redirect($this->generateUrl('baikal_admin_user_list'));
        }

        return $this->render('BaikalAdminBundle:User:form.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function editAction(Request $request, User $user) {

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

        return $this->render('BaikalAdminBundle:User:form.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }
}
