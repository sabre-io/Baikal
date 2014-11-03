<?php

namespace Baikal\ModelBundle\Form\Type\Calendar;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Validator\Constraints\NotBlank;

class EventType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
            ->add('title', 'text', array(
                'label' => 'Title',
                'constraints' => array(
                    new NotBlank(),
                )
            ))
            ->add('start', 'datetime', array(
                'label' => 'Start',
                'widget' => 'single_text',
                'format' => "yyyy-MM-dd'T'HH:mm:ssZZZZZ",
                'date_format' => "yyyy-MM-dd'T'HH:mm:ssZZZZZ",
                'constraints' => array(
                    new NotBlank(),
                )
            ))
            ->add('end', 'datetime', array(
                'label' => 'End',
                'widget' => 'single_text',
                'format' => "yyyy-MM-dd'T'HH:mm:ssZZZZZ",
                'date_format' => "yyyy-MM-dd'T'HH:mm:ssZZZZZ",
                'constraints' => array(
                    new NotBlank(),
                )
            ))
            ->add('busy', 'checkbox', array(
                'label' => 'Busy ?'
            ))
            ->add('recurrence_freq', 'text', array(
                'label' => 'recurrence_freq',
            ))
            ->add('recurrence_interval', 'text', array(
                'label' => 'recurrence_interval',
            ))
            ->add('recurrence_count', 'text', array(
                'label' => 'recurrence_count',
            ))
            ->add('recurrence_until', 'text', array(
                'label' => 'recurrence_until',
            ))
            ->add('recurrence_weekly_byday', 'text', array(
                'label' => 'recurrence_weekly_byday',
            ))
            ->add('recurrence_monthly_byweekday_day', 'text', array(
                'label' => 'recurrence_monthly_byweekday_day',
            ))
            ->add('recurrence_monthly_byweekday_nth', 'text', array(
                'label' => 'recurrence_monthly_byweekday_nth',
            ))
            ->add('recurrence_monthly_byweekday_fromend', 'text', array(
                'label' => 'recurrence_monthly_byweekday_fromend',
            ))
            ->add('recurrence_monthly_bymonthday', 'text', array(
                'label' => 'recurrence_monthly_bymonthday',
            ))
            ->add('recurrence_yearly_bymonth', 'text', array(
                'label' => 'recurrence_yearly_bymonth',
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Baikal\ModelBundle\Entity\Event'
        ));
    }

    public function getName() {
        return 'baikal_modelbundle_event';
    }
}