<?php

namespace Sabre\VObject;

class PropertyTest extends \PHPUnit_Framework_TestCase {

    public function testToString() {

        $property = new Property('propname','propvalue');
        $this->assertEquals('PROPNAME', $property->name);
        $this->assertEquals('propvalue', $property->value);
        $this->assertEquals('propvalue', $property->__toString());
        $this->assertEquals('propvalue', (string)$property);

    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCreateNonScalar() {

        $property = new Property('propname',array());

    }

    public function testParameterExists() {

        $property = new Property('propname','propvalue');
        $property->parameters[] = new Parameter('paramname','paramvalue');

        $this->assertTrue(isset($property['PARAMNAME']));
        $this->assertTrue(isset($property['paramname']));
        $this->assertFalse(isset($property['foo']));

    }

    public function testParameterGet() {

        $property = new Property('propname','propvalue');
        $property->parameters[] = new Parameter('paramname','paramvalue');

        $this->assertInstanceOf('Sabre\\VObject\\Parameter',$property['paramname']);

    }

    public function testParameterNotExists() {

        $property = new Property('propname','propvalue');
        $property->parameters[] = new Parameter('paramname','paramvalue');

        $this->assertInternalType('null',$property['foo']);

    }

    public function testParameterMultiple() {

        $property = new Property('propname','propvalue');
        $property->parameters[] = new Parameter('paramname','paramvalue');
        $property->parameters[] = new Parameter('paramname','paramvalue');

        $this->assertInstanceOf('Sabre\\VObject\\Parameter',$property['paramname']);
        $this->assertEquals(2,count($property['paramname']));

    }

    public function testSetParameterAsString() {

        $property = new Property('propname','propvalue');
        $property['paramname'] = 'paramvalue';

        $this->assertEquals(1,count($property->parameters));
        $this->assertInstanceOf('Sabre\\VObject\\Parameter', $property->parameters[0]);
        $this->assertEquals('PARAMNAME',$property->parameters[0]->name);
        $this->assertEquals('paramvalue',$property->parameters[0]->value);

    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetParameterAsStringNoKey() {

        $property = new Property('propname','propvalue');
        $property[] = 'paramvalue';

    }

    public function testSetParameterObject() {

        $property = new Property('propname','propvalue');
        $param = new Parameter('paramname','paramvalue');

        $property[] = $param;

        $this->assertEquals(1,count($property->parameters));
        $this->assertEquals($param, $property->parameters[0]);

    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetParameterObjectWithKey() {

        $property = new Property('propname','propvalue');
        $param = new Parameter('paramname','paramvalue');

        $property['key'] = $param;

    }


    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetParameterObjectRandomObject() {

        $property = new Property('propname','propvalue');
        $property[] = new \StdClass();

    }

    public function testUnsetParameter() {

        $property = new Property('propname','propvalue');
        $param = new Parameter('paramname','paramvalue');
        $property->parameters[] = $param;

        unset($property['PARAMNAME']);
        $this->assertEquals(0,count($property->parameters));

    }

    public function testParamCount() {

        $property = new Property('propname','propvalue');
        $param = new Parameter('paramname','paramvalue');
        $property->parameters[] = $param;
        $property->parameters[] = clone $param;

        $this->assertEquals(2,count($property->parameters));

    }

    public function testSerialize() {

        $property = new Property('propname','propvalue');

        $this->assertEquals("PROPNAME:propvalue\r\n",$property->serialize());

    }

    public function testSerializeParam() {

        $property = new Property('propname','propvalue');
        $property->parameters[] = new Parameter('paramname','paramvalue');
        $property->parameters[] = new Parameter('paramname2','paramvalue2');

        $this->assertEquals("PROPNAME;PARAMNAME=paramvalue;PARAMNAME2=paramvalue2:propvalue\r\n",$property->serialize());

    }

    public function testSerializeNewLine() {

        $property = new Property('propname',"line1\nline2");

        $this->assertEquals("PROPNAME:line1\\nline2\r\n",$property->serialize());

    }

    public function testSerializeLongLine() {

        $value = str_repeat('!',200);
        $property = new Property('propname',$value);

        $expected = "PROPNAME:" . str_repeat('!',66) . "\r\n " . str_repeat('!',74) . "\r\n " . str_repeat('!',60) . "\r\n";

        $this->assertEquals($expected,$property->serialize());

    }

    public function testSerializeUTF8LineFold() {

        $value = str_repeat('!',65) . "\xc3\xa4bla"; // inserted umlaut-a
        $property = new Property('propname', $value);
        $expected = "PROPNAME:" . str_repeat('!',65) . "\r\n \xc3\xa4bla\r\n";
        $this->assertEquals($expected, $property->serialize());

    }

    public function testGetIterator() {

        $it = new ElementList(array());
        $property = new Property('propname','propvalue');
        $property->setIterator($it);
        $this->assertEquals($it,$property->getIterator());

    }


    public function testGetIteratorDefault() {

        $property = new Property('propname','propvalue');
        $it = $property->getIterator();
        $this->assertTrue($it instanceof ElementList);
        $this->assertEquals(1,count($it));

    }

    function testAddScalar() {

        $property = new Property('EMAIL');

        $property->add('myparam','value');

        $this->assertEquals(1, count($property->parameters));

        $this->assertTrue($property->parameters[0] instanceof Parameter);
        $this->assertEquals('MYPARAM',$property->parameters[0]->name);
        $this->assertEquals('value',$property->parameters[0]->value);

    }

    function testAddParameter() {

        $prop = new Property('EMAIL');

        $prop->add(new Parameter('MYPARAM','value'));

        $this->assertEquals(1, count($prop->parameters));
        $this->assertEquals('MYPARAM',$prop['myparam']->name);

    }

    function testAddParameterTwice() {

        $prop = new Property('EMAIL');

        $prop->add(new Parameter('MYPARAM', 'value1'));
        $prop->add(new Parameter('MYPARAM', 'value2'));

        $this->assertEquals(2, count($prop->parameters));

        $this->assertEquals('MYPARAM',$prop['MYPARAM']->name);

    }

    /**
     * @expectedException InvalidArgumentException
     */
    function testAddArgFail() {

        $prop = new Property('EMAIL');
        $prop->add(new Parameter('MPARAM'),'hello');

    }

    /**
     * @expectedException InvalidArgumentException
     */
    function testAddArgFail2() {

        $property = new Property('EMAIL','value');
        $property->add(array());

    }

    /**
     * @expectedException InvalidArgumentException
     */
    function testAddArgFail3() {

        $property = new Property('EMAIL','value');
        $property->add('hello',array());

    }

    function testClone() {

        $property = new Property('EMAIL','value');
        $property['FOO'] = 'BAR';

        $property2 = clone $property;
        
        $property['FOO'] = 'BAZ';
        $this->assertEquals('BAR', (string)$property2['FOO']);

    }

    function testCreateParams() {

        $property = Property::create('X-PROP', 'value', array(
            'param1' => 'value1',
            'param2' => array('value2', 'value3')
        ));

        $this->assertEquals(1, count($property['PARAM1']));
        $this->assertEquals(2, count($property['PARAM2']));

    }

    function testValidateNonUTF8() {

        $property = Property::create('X-PROP', "Bla\x00");
        $result = $property->validate(Property::REPAIR);

        $this->assertEquals('Property is not valid UTF-8!', $result[0]['message']);
        $this->assertEquals('Bla', $property->value);

    }


    function testValidateBadPropertyName() {

        $property = Property::create("X_*&PROP*", "Bla");
        $result = $property->validate(Property::REPAIR);

        $this->assertEquals($result[0]['message'], 'The propertyname: X_*&PROP* contains invalid characters. Only A-Z, 0-9 and - are allowed');
        $this->assertEquals('X-PROP', $property->name);

    }

}
