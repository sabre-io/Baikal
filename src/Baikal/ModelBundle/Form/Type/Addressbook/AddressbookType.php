<?php

namespace Baikal\ModelBundle\Form\Type\Addressbook;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints\NotBlank;

class AddressbookType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
            ->add('displayname', 'text', array(
                "label" => "Display name",
                'constraints' => array(
                    new NotBlank(),
                )
            ))
            ->add('description', 'text', array(
                "label" => "Description",
            ));
    }

    public function getName() {
        return 'addressbook';
    }
}