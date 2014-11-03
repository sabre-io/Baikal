<?php

namespace Baikal\ModelBundle\Form\Type\User;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints\NotBlank,
    Symfony\Component\Validator\Constraints\Email;

class CreateUserType extends AbstractUserType {

    public function buildForm(FormBuilderInterface $builder, array $options) {

        parent::buildForm($builder, $options);

        $builder->add('username', 'text', array(
            "label" => "Username",
            'constraints' => array(
                new NotBlank(),
            )
        ));

        $passwordAttributes = $builder->get('password')->getAttributes();
        $passwordAttributes = $passwordAttributes['data_collector/passed_options'];
        $passwordAttributes['options'] = array('required' => TRUE);
        $passwordAttributes['constraints'] = array(new NotBlank());
        
        $builder
            ->remove('password')
            ->add('password', 'repeated', $passwordAttributes);
    }

    public function getName() {
        return 'user';
    }
}