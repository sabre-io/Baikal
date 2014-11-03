<?php

namespace Baikal\ModelBundle\Form\Type\User;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints\NotBlank,
    Symfony\Component\Validator\Constraints\Email;

abstract class AbstractUserType extends AbstractType {

    abstract public function getName();
    
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
            ->add('displayname', 'text', array(
                "label" => "Display name",
                'constraints' => array(
                    new NotBlank(),
                )
            ))
            ->add('email', 'email', array(
                "label" => "Email",
                'constraints' => array(
                    new NotBlank(),
                    new Email()
                )
            ))
            ->add('roles', 'choice', array(
                'multiple' => TRUE,
                'expanded' => TRUE,
                'choices' => array(
                    'ROLE_FRONTEND_USER' => "Frontend User",
                    'ROLE_ADMIN' => "Administrator"
                ),
                'constraints' => array(
                    new NotBlank(),
                )
            ))
            ->add('password', 'repeated', array(
                'type' => 'password',
                'invalid_message' => 'Passwords do not match.',
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Password (confirmation)'),
            ));
    }
}