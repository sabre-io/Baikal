<?php

namespace Baikal\ModelBundle\Form\Type\OAuth;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints\NotBlank;

use OAuth2\OAuth2;

class ClientType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
            ->add('name', 'text', array(
                'label' => 'Application name',
                'constraints' => array(
                    new NotBlank(),
                )
            ))
            ->add('description', 'text', array(
                'label' => 'Description'
            ))
            ->add('homepageurl', 'text', array(
                'label' => 'Homepage URL',
                'constraints' => array(
                    new NotBlank(),
                )
            ))
            ->add('redirecturi', 'text', array(
                'label' => 'Redirect URI',
                'constraints' => array(
                    new NotBlank(),
                )
            ))
            ->add('allowed_grant_types', 'choice', array(
                'multiple' => TRUE,
                'expanded' => TRUE,
                'choices' => array(
                    OAuth2::GRANT_TYPE_AUTH_CODE => OAuth2::GRANT_TYPE_AUTH_CODE,
                    OAuth2::GRANT_TYPE_IMPLICIT => OAuth2::GRANT_TYPE_IMPLICIT,
                    OAuth2::GRANT_TYPE_USER_CREDENTIALS => OAuth2::GRANT_TYPE_USER_CREDENTIALS,
                    OAuth2::GRANT_TYPE_CLIENT_CREDENTIALS => OAuth2::GRANT_TYPE_CLIENT_CREDENTIALS,
                    OAuth2::GRANT_TYPE_REFRESH_TOKEN => OAuth2::GRANT_TYPE_REFRESH_TOKEN,
                    OAuth2::GRANT_TYPE_EXTENSIONS => OAuth2::GRANT_TYPE_EXTENSIONS,
                ),
                'constraints' => array(
                    new NotBlank(),
                )
            ));
    }

    public function getName() {
        return 'oauthclient';
    }
}