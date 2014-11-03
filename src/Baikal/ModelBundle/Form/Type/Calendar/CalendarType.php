<?php

namespace Baikal\ModelBundle\Form\Type\Calendar;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints\NotBlank;

class CalendarType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
            ->add('displayname', 'text', array(
                "label" => "Display name"
            ))
            ->add('description', 'text', array(
                "label" => "Description"
            ))
            ->add('calendarcolor', 'text', array(
                "label" => "Calendar color"
            ))/*
            ->add('todos', 'checkbox', array(
                "label" => "Todos",
            ))*/;
    }

    public function getName() {
        return 'calendar';
    }
}