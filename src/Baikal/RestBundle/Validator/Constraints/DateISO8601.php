<?php

namespace Baikal\RestBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DateISO8601 extends Constraint {
    public $message = 'The date is not valid. Expecting ISO-8601; did you forget to urlencode the date ?';
}