<?php

namespace Baikal\FrontendBundle\Form\Type\Calendar;

use Symfony\Component\Form\FormBuilderInterface;

use Baikal\ModelBundle\Form\Type\Calendar\CalendarType as BaseCalendarType;

class CalendarType extends BaseCalendarType {

    public function getName() {
        return 'calendar_frontend';
    }
}