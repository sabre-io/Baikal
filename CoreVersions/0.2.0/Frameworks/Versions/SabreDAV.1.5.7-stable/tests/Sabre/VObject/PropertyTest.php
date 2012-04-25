<?php

class Sabre_VObject_PropertyTest extends PHPUnit_Framework_TestCase {

    public function testToString() {

        $property = new Sabre_VObject_Property('propname','propvalue');
        $this->assertEquals('PROPNAME', $property->name);
        $this->assertEquals('propvalue', $property->value);
        $this->assertEquals('propvalue', $property->__toString());
        $this->assertEquals('propvalue', (string)$property);

    }

    public function testParameterExists() {

        $property = new Sabre_VObject_Property('propname','propvalue');
        $property->parameters[] = new Sabre_VObject_Parameter('paramname','paramvalue');

        $this->assertTrue(isset($property['PARAMNAME']));
        $this->assertTrue(isset($property['paramname']));
        $this->assertFalse(isset($property['foo']));

    }

    public function testParameterGet() {

        $property = new Sabre_VObject_Property('propname','propvalue');
        $property->parameters[] = new Sabre_VObject_Parameter('paramname','paramvalue');
        
        $this->assertInstanceOf('Sabre_VObject_Parameter',$property['paramname']);

    }

    public function testParameterNotExists() {

        $property = new Sabre_VObject_Property('propname','propvalue');
        $property->parameters[] = new Sabre_VObject_Parameter('paramname','paramvalue');
        
        $this->assertInternalType('null',$property['foo']);

    }

    public function testParameterMultiple() {

        $property = new Sabre_VObject_Property('propname','propvalue');
        $property->parameters[] = new Sabre_VObject_Parameter('paramname','paramvalue');
        $property->parameters[] = new Sabre_VObject_Parameter('paramname','paramvalue');
        
        $this->assertInstanceOf('Sabre_VObject_Parameter',$property['paramname']);
        $this->assertEquals(2,count($property['paramname']));

    }

    public function testSetParameterAsString() {

        $property = new Sabre_VObject_Property('propname','propvalue');
        $property['paramname'] = 'paramvalue';

        $this->assertEquals(1,count($property->parameters));
        $this->assertInstanceOf('Sabre_VObject_Parameter', $property->parameters[0]);
        $this->assertEquals('PARAMNAME',$property->parameters[0]->name);
        $this->assertEquals('paramvalue',$property->parameters[0]->value);

    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetParameterAsStringNoKey() {

        $property = new Sabre_VObject_Property('propname','propvalue');
        $property[] = 'paramvalue';

    }

    public function testSetParameterObject() {

        $property = new Sabre_VObject_Property('propname','propvalue');
        $param = new Sabre_VObject_Parameter('paramname','paramvalue');

        $property[] = $param;

        $this->assertEquals(1,count($property->parameters));
        $this->assertEquals($param, $property->parameters[0]);

    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetParameterObjectWithKey() {

        $property = new Sabre_VObject_Property('propname','propvalue');
        $param = new Sabre_VObject_Parameter('paramname','paramvalue');

        $property['key'] = $param;

    }


    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetParameterObjectRandomObject() {

        $property = new Sabre_VObject_Property('propname','propvalue');
        $property[] = new StdClass(); 

    }

    public function testUnsetParameter() {

        $property = new Sabre_VObject_Property('propname','propvalue');
        $param = new Sabre_VObject_Parameter('paramname','paramvalue');
        $property->parameters[] = $param;

        unset($property['PARAMNAME']);
        $this->assertEquals(0,count($property->parameters));

    }

    public function testParamCount() {

        $property = new Sabre_VObject_Property('propname','propvalue');
        $param = new Sabre_VObject_Parameter('paramname','paramvalue');
        $property->parameters[] = $param;
        $property->parameters[] = clone $param;

        $this->assertEquals(2,count($property->parameters));

    }

    public function testSerialize() {

        $property = new Sabre_VObject_Property('propname','propvalue');

        $this->assertEquals("PROPNAME:propvalue\r\n",$property->serialize());

    }

    public function testSerializeParam() {

        $property = new Sabre_VObject_Property('propname','propvalue');
        $property->parameters[] = new Sabre_VObject_Parameter('paramname','paramvalue');
        $property->parameters[] = new Sabre_VObject_Parameter('paramname2','paramvalue2');

        $this->assertEquals("PROPNAME;PARAMNAME=paramvalue;PARAMNAME2=paramvalue2:propvalue\r\n",$property->serialize());

    }

    public function testSerializeNewLine() {

        $property = new Sabre_VObject_Property('propname',"line1\nline2");

        $this->assertEquals("PROPNAME:line1\\nline2\r\n",$property->serialize());

    }

    public function testSerializeLongLine() {

        $value = str_repeat('!',200);
        $property = new Sabre_VObject_Property('propname',$value);

        $expected = "PROPNAME:" . str_repeat('!',66) . "\r\n " . str_repeat('!',74) . "\r\n " . str_repeat('!',60) . "\r\n";

        $this->assertEquals($expected,$property->serialize());

    }

    public function testSerializeUTF8LineFold() {

        $value = str_repeat('!',65) . "\xc3\xa4bla"; // inserted umlaut-a
        $property = new Sabre_VObject_Property('propname', $value);
        $expected = "PROPNAME:" . str_repeat('!',65) . "\r\n \xc3\xa4bla\r\n";
        $this->assertEquals($expected, $property->serialize());

    }

    public function testGetIterator() {

        $it = new Sabre_VObject_ElementList(array());
        $property = new Sabre_VObject_Property('propname','propvalue', $it);
        $this->assertEquals($it,$property->getIterator());

    }


    public function testGetIteratorDefault() {

        $property = new Sabre_VObject_Property('propname','propvalue');
        $it = $property->getIterator();
        $this->assertTrue($it instanceof Sabre_VObject_ElementList);
        $this->assertEquals(1,count($it));

    }

    function testAddScalar() {

        $property = new Sabre_VObject_Property('EMAIL');

        $property->add('myparam','value');

        $this->assertEquals(1, count($property->parameters));

        $this->assertTrue($property->parameters[0] instanceof Sabre_VObject_Parameter);
        $this->assertEquals('MYPARAM',$property->parameters[0]->name); 
        $this->assertEquals('value',$property->parameters[0]->value); 

    }

    function testAddParameter() {

        $prop = new Sabre_VObject_Property('EMAIL');

        $prop->add(new Sabre_VObject_Parameter('MYPARAM','value'));

        $this->assertEquals(1, count($prop->parameters));
        $this->assertEquals('MYPARAM',$prop['myparam']->name); 

    }

    function testAddParameterTwice() {

        $prop = new Sabre_VObject_Property('EMAIL');

        $prop->add(new Sabre_VObject_Parameter('MYPARAM', 'value1'));
        $prop->add(new Sabre_VObject_Parameter('MYPARAM', 'value2'));

        $this->assertEquals(2, count($prop->parameters));

        $this->assertEquals('MYPARAM',$prop['MYPARAM']->name); 

    }

    /**
     * @expectedException InvalidArgumentException
     */
    function testAddArgFail() {

        $prop = new Sabre_VObject_Property('EMAIL');
        $prop->add(new Sabre_VObject_Parameter('MPARAM'),'hello');

    }

    /**
     * @expectedException InvalidArgumentException
     */
    function testAddArgFail2() {

        $property = new Sabre_VObject_Property('EMAIL','value');
        $property->add(array());

    }

    /**
     * @expectedException InvalidArgumentException
     */
    function testAddArgFail3() {

        $property = new Sabre_VObject_Property('EMAIL','value');
        $property->add('hello',array());

    }

}
