<?php

namespace Baikal\ModelBundle\Form\Type\User;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints\NotBlank,
    Symfony\Component\Validator\Constraints\Email;

class EditUserType extends AbstractUserType {

    public function getName() {
        return 'user_edit';
    }
}