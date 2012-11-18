<?php

namespace Sabre\VObject;

class ReaderTest extends \PHPUnit_Framework_TestCase {

    function testReadComponent() {

        $data = "BEGIN:VCALENDAR\r\nEND:VCALENDAR";

        $result = Reader::read($data);

        $this->assertInstanceOf('Sabre\\VObject\\Component', $result);
        $this->assertEquals('VCALENDAR', $result->name);
        $this->assertEquals(0, count($result->children));

    }

    function testReadComponentUnixNewLine() {

        $data = "BEGIN:VCALENDAR\nEND:VCALENDAR";

        $result = Reader::read($data);

        $this->assertInstanceOf('Sabre\\VObject\\Component', $result);
        $this->assertEquals('VCALENDAR', $result->name);
        $this->assertEquals(0, count($result->children));

    }

    function testReadComponentMacNewLine() {

        $data = "BEGIN:VCALENDAR\rEND:VCALENDAR";

        $result = Reader::read($data);

        $this->assertInstanceOf('Sabre\\VObject\\Component', $result);
        $this->assertEquals('VCALENDAR', $result->name);
        $this->assertEquals(0, count($result->children));

    }

    function testReadComponentLineFold() {

        $data = "BEGIN:\r\n\tVCALENDAR\r\nE\r\n ND:VCALENDAR";

        $result = Reader::read($data);

        $this->assertInstanceOf('Sabre\\VObject\\Component', $result);
        $this->assertEquals('VCALENDAR', $result->name);
        $this->assertEquals(0, count($result->children));

    }

    /**
     * @expectedException Sabre\VObject\ParseException
     */
    function testReadCorruptComponent() {

        $data = "BEGIN:VCALENDAR\r\nEND:FOO";

        $result = Reader::read($data);

    }

    function testReadProperty() {

        $data = "PROPNAME:propValue";
        $result = Reader::read($data);

        $this->assertInstanceOf('Sabre\\VObject\\Property', $result);
        $this->assertEquals('PROPNAME', $result->name);
        $this->assertEquals('propValue', $result->value);

    }

    function testReadPropertyWithNewLine() {

        $data = 'PROPNAME:Line1\\nLine2\\NLine3\\\\Not the 4th line!';
        $result = Reader::read($data);

        $this->assertInstanceOf('Sabre\\VObject\\Property', $result);
        $this->assertEquals('PROPNAME', $result->name);
        $this->assertEquals("Line1\nLine2\nLine3\\Not the 4th line!", $result->value);

    }

    function testReadMappedProperty() {

        $data = "DTSTART:20110529";
        $result = Reader::read($data);

        $this->assertInstanceOf('Sabre\\VObject\\Property\\DateTime', $result);
        $this->assertEquals('DTSTART', $result->name);
        $this->assertEquals('20110529', $result->value);

    }

    function testReadMappedPropertyGrouped() {

        $data = "foo.DTSTART:20110529";
        $result = Reader::read($data);

        $this->assertInstanceOf('Sabre\\VObject\\Property\\DateTime', $result);
        $this->assertEquals('DTSTART', $result->name);
        $this->assertEquals('20110529', $result->value);

    }


    /**
     * @expectedException Sabre\VObject\ParseException
     */
    function testReadBrokenLine() {

        $data = "PROPNAME;propValue";
        $result = Reader::read($data);

    }

    function testReadPropertyInComponent() {

        $data = array(
            "BEGIN:VCALENDAR",
            "PROPNAME:propValue",
            "END:VCALENDAR"
        );

        $result = Reader::read(implode("\r\n",$data));

        $this->assertInstanceOf('Sabre\\VObject\\Component', $result);
        $this->assertEquals('VCALENDAR', $result->name);
        $this->assertEquals(1, count($result->children));
        $this->assertInstanceOf('Sabre\\VObject\\Property', $result->children[0]);
        $this->assertEquals('PROPNAME', $result->children[0]->name);
        $this->assertEquals('propValue', $result->children[0]->value);

    }
    function testReadNestedComponent() {

        $data = array(
            "BEGIN:VCALENDAR",
            "BEGIN:VTIMEZONE",
            "BEGIN:DAYLIGHT",
            "END:DAYLIGHT",
            "END:VTIMEZONE",
            "END:VCALENDAR"
        );

        $result = Reader::read(implode("\r\n",$data));

        $this->assertInstanceOf('Sabre\\VObject\\Component', $result);
        $this->assertEquals('VCALENDAR', $result->name);
        $this->assertEquals(1, count($result->children));
        $this->assertInstanceOf('Sabre\\VObject\\Component', $result->children[0]);
        $this->assertEquals('VTIMEZONE', $result->children[0]->name);
        $this->assertEquals(1, count($result->children[0]->children));
        $this->assertInstanceOf('Sabre\\VObject\\Component', $result->children[0]->children[0]);
        $this->assertEquals('DAYLIGHT', $result->children[0]->children[0]->name);


    }

    function testReadPropertyParameter() {

        $data = "PROPNAME;PARAMNAME=paramvalue:propValue";
        $result = Reader::read($data);

        $this->assertInstanceOf('Sabre\\VObject\\Property', $result);
        $this->assertEquals('PROPNAME', $result->name);
        $this->assertEquals('propValue', $result->value);
        $this->assertEquals(1, count($result->parameters));
        $this->assertEquals('PARAMNAME', $result->parameters[0]->name);
        $this->assertEquals('paramvalue', $result->parameters[0]->value);

    }

    function testReadPropertyNoValue() {

        $data = "PROPNAME;PARAMNAME:propValue";
        $result = Reader::read($data);

        $this->assertInstanceOf('Sabre\\VObject\\Property', $result);
        $this->assertEquals('PROPNAME', $result->name);
        $this->assertEquals('propValue', $result->value);
        $this->assertEquals(1, count($result->parameters));
        $this->assertEquals('PARAMNAME', $result->parameters[0]->name);
        $this->assertEquals('', $result->parameters[0]->value);

    }

    function testReadPropertyParameterExtraColon() {

        $data = "PROPNAME;PARAMNAME=paramvalue:propValue:anotherrandomstring";
        $result = Reader::read($data);

        $this->assertInstanceOf('Sabre\\VObject\\Property', $result);
        $this->assertEquals('PROPNAME', $result->name);
        $this->assertEquals('propValue:anotherrandomstring', $result->value);
        $this->assertEquals(1, count($result->parameters));
        $this->assertEquals('PARAMNAME', $result->parameters[0]->name);
        $this->assertEquals('paramvalue', $result->parameters[0]->value);

    }

    function testReadProperty2Parameters() {

        $data = "PROPNAME;PARAMNAME=paramvalue;PARAMNAME2=paramvalue2:propValue";
        $result = Reader::read($data);

        $this->assertInstanceOf('Sabre\\VObject\\Property', $result);
        $this->assertEquals('PROPNAME', $result->name);
        $this->assertEquals('propValue', $result->value);
        $this->assertEquals(2, count($result->parameters));
        $this->assertEquals('PARAMNAME', $result->parameters[0]->name);
        $this->assertEquals('paramvalue', $result->parameters[0]->value);
        $this->assertEquals('PARAMNAME2', $result->parameters[1]->name);
        $this->assertEquals('paramvalue2', $result->parameters[1]->value);

    }

    function testReadPropertyParameterQuoted() {

        $data = "PROPNAME;PARAMNAME=\"paramvalue\":propValue";
        $result = Reader::read($data);

        $this->assertInstanceOf('Sabre\\VObject\\Property', $result);
        $this->assertEquals('PROPNAME', $result->name);
        $this->assertEquals('propValue', $result->value);
        $this->assertEquals(1, count($result->parameters));
        $this->assertEquals('PARAMNAME', $result->parameters[0]->name);
        $this->assertEquals('paramvalue', $result->parameters[0]->value);

    }
    function testReadPropertyParameterNewLines() {

        $data = "PROPNAME;PARAMNAME=paramvalue1\\nvalue2\\\\nvalue3:propValue";
        $result = Reader::read($data);

        $this->assertInstanceOf('Sabre\\VObject\\Property', $result);
        $this->assertEquals('PROPNAME', $result->name);
        $this->assertEquals('propValue', $result->value);

        $this->assertEquals(1, count($result->parameters));
        $this->assertEquals('PARAMNAME', $result->parameters[0]->name);
        $this->assertEquals("paramvalue1\nvalue2\\nvalue3", $result->parameters[0]->value);

    }

    function testReadPropertyParameterQuotedColon() {

        $data = "PROPNAME;PARAMNAME=\"param:value\":propValue";
        $result = Reader::read($data);

        $this->assertInstanceOf('Sabre\\VObject\\Property', $result);
        $this->assertEquals('PROPNAME', $result->name);
        $this->assertEquals('propValue', $result->value);
        $this->assertEquals(1, count($result->parameters));
        $this->assertEquals('PARAMNAME', $result->parameters[0]->name);
        $this->assertEquals('param:value', $result->parameters[0]->value);

    }

    function testReadForgiving() {

        $data = array(
            "BEGIN:VCALENDAR",
            "X_PROP:propValue",
            "END:VCALENDAR"
        );

        $caught = false;
        try {
            $result = Reader::read(implode("\r\n",$data));
        } catch (ParseException $e) {
            $caught = true;
        }

        $this->assertEquals(true, $caught);

        $result = Reader::read(implode("\r\n",$data), Reader::OPTION_FORGIVING);

        $expected = implode("\r\n", array(
            "BEGIN:VCALENDAR",
            "X_PROP:propValue",
            "END:VCALENDAR",
            ""
        ));

        $this->assertEquals($expected, $result->serialize());


    }

    function testReadWithInvalidLine() {

        $data = array(
            "BEGIN:VCALENDAR",
            "DESCRIPTION:propValue",
            "Yes, we've actually seen a file with non-idented property values on multiple lines",
            "END:VCALENDAR"
        );

        $caught = false;
        try {
            $result = Reader::read(implode("\r\n",$data));
        } catch (ParseException $e) {
            $caught = true;
        }

        $this->assertEquals(true, $caught);

        $result = Reader::read(implode("\r\n",$data), Reader::OPTION_IGNORE_INVALID_LINES);

        $expected = implode("\r\n", array(
            "BEGIN:VCALENDAR",
            "DESCRIPTION:propValue",
            "END:VCALENDAR",
            ""
        ));

        $this->assertEquals($expected, $result->serialize());


    }

}
