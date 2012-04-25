<?php

class Sabre_VObject_ParameterTest extends PHPUnit_Framework_TestCase {

    function testSetup() {

        $param = new Sabre_VObject_Parameter('name','value');
        $this->assertEquals('NAME',$param->name);
        $this->assertEquals('value',$param->value);

    }

    function testCastToString() {

        $param = new Sabre_VObject_Parameter('name','value');
        $this->assertEquals('value',$param->__toString());
        $this->assertEquals('value',(string)$param);

    }

}
