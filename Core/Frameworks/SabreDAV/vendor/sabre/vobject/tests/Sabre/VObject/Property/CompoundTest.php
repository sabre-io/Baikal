<?php

namespace Sabre\VObject\Property;
use Sabre\VObject\Component;

class CompoundTest extends \PHPUnit_Framework_TestCase {

    function testSetParts() {

        $arr = array(
            'ABC, Inc.',
            'North American Division',
            'Marketing;Sales',
        );

        $elem = new Compound('ORG');
        $elem->setParts($arr);

        $this->assertEquals('ABC\, Inc.;North American Division;Marketing\;Sales', $elem->value);
        $this->assertEquals(3, count($elem->getParts()));
        $parts = $elem->getParts();
        $this->assertEquals('Marketing;Sales', $parts[2]);

    }

    function testGetParts() {

        $str = 'ABC\, Inc.;North American Division;Marketing\;Sales';

        $elem = new Compound('ORG', $str);

        $this->assertEquals(3, count($elem->getParts()));
        $parts = $elem->getParts();
        $this->assertEquals('Marketing;Sales', $parts[2]);
    }

    function testGetPartsDefaultDelimiter() {

        $str = 'Hi!;Hello!';

        $elem = new Compound('X-FOO', $str);

        $this->assertEquals(array(
            'Hi!',
            'Hello!',
        ), $elem->getParts());

    }

    function testGetPartsNull() {

        $str = 'ABC\, Inc.;North American Division;Marketing\;Sales';

        $elem = new Compound('ORG', null);

        $this->assertEquals(0, count($elem->getParts()));

    }
}
