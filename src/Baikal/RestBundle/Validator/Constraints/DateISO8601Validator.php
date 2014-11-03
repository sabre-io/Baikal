<?php

namespace Baikal\RestBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint,
    Symfony\Component\Validator\ConstraintValidator;

class DateISO8601Validator extends ConstraintValidator {
    
    public function validate($value, Constraint $constraint) {

        if(!$this->validateDate($value)) {
            $this->context->addViolation($constraint->message);
        }
    }

    protected function validateDate($dateStr) {
        if(preg_match('/^([\+-]?\d{4}(?!\d{2}\b))((-?)((0[1-9]|1[0-2])(\3([12]\d|0[1-9]|3[01]))?|W([0-4]\d|5[0-2])(-?[1-7])?|(00[1-9]|0[1-9]\d|[12]\d{2}|3([0-5]\d|6[1-6])))([T\s]((([01]\d|2[0-3])((:?)[0-5]\d)?|24\:?00)([\.,]\d+(?!:))?)?(\17[0-5]\d([\.,]\d+)?)?([zZ]|([\+-])([01]\d|2[0-3]):?([0-5]\d)?)?)?)?$/', $dateStr) <= 0) {
            return FALSE;
        }

        try {
            $date = new \DateTime($dateStr);
        } catch(\Exception $e) {
            return FALSE;
        }

        return TRUE;
    }
}