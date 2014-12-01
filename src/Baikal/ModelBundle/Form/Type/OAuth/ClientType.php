<?php

namespace Baikal\ModelBundle\Form\Type\OAuth;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints\NotBlank;

class ClientType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
            ->add('name', 'text', array(
                "label" => "Application name",
                'constraints' => array(
                    new NotBlank(),
                )
            ))
            ->add('description', 'text', array(
                "label" => "Description"
            ))
            ->add('homepageurl', 'text', array(
                "label" => "Homepage URL",
                'constraints' => array(
                    new NotBlank(),
                )
            ))
            ->add('redirecturi', 'text', array(
                "label" => "Redirect URI",
                'constraints' => array(
                    new NotBlank(),
                )
            ));
    }

    public function getName() {
        return 'oauthclient';
    }
}