<?php

namespace Baikal\Domain;

abstract class DomainObject {

    /**
     * Creates an instance of the Domain object, based on an array of values.
     *
     * This method will throw an InvalidArgumentException if unknown values
     * are passed.
     */
    static function fromArray(array $values) {

        $obj = new static();
        foreach ($values as $k => $v) {
            if (property_exists($obj, $k)) {
                $obj->$k = $v;
            } else {
                throw new \InvalidArgumentException('Invalid property: ' . $k . ' for class ' . get_class($obj));
            }
        }
        return $obj;

    }

    /**
     * Creates an instance of the Domain object, based on an array of values.
     *
     * This function works identical to fromArray, but unknown keys are
     * ignored.
     */
    static function fromPostForm(array $values) {

        $obj = new static();
        foreach ($values as $k => $v) {
            if (property_exists($obj, $k)) {
                $obj->$k = $v;
            }
        }
        return $obj;

    }

    /**
     * Validator
     *
     * Called before insert and update operations
     *
     * Should throw an \InvalidArgumentException if there's a validation problem.
     * @return void
     */
    function validate() {

    }

    /**
     * Validator function, specific for update operations.
     */
    function validateForUpdate() {

        $this->validate();

    }

    /**
     * Validator function, specific for insert operations
     */
    function validateForCreate() {

        $this->validate();

    }

}
