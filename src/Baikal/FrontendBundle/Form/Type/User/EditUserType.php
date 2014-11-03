<?php

namespace Baikal\FrontendBundle\Form\Type\User;

use Symfony\Component\Form\FormBuilderInterface;

use Baikal\ModelBundle\Form\Type\User\EditUserType as BaseEditUserType;

class EditUserType extends BaseEditUserType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        parent::buildForm($builder, $options);
        $builder->remove('roles');
    }

    public function getName() {
        return 'user_edit_frontend';
    }
}