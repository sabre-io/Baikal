<?php

namespace Sabre\VObject\Component;

use Sabre\VObject;

class VCardTest extends \PHPUnit_Framework_TestCase {

    /**
     * @dataProvider validateData
     */
    function testValidate($input, $expectedWarnings, $expectedRepairedOutput) {

        $vcard = VObject\Reader::read($input);

        $warnings = $vcard->validate();

        $warnMsg = array();
        foreach($warnings as $warning) {
            $warnMsg[] = $warning['message'];
        }

        $this->assertEquals($expectedWarnings, $warnMsg);

        $vcard->validate(VObject\Component::REPAIR);

        $this->assertEquals(
            $expectedRepairedOutput,
            $vcard->serialize()
        );

    }

    public function validateData() {

        $tests = array();

        // Correct
        $tests[] = array(
            "BEGIN:VCARD\r\nVERSION:4.0\r\nFN:John Doe\r\nEND:VCARD\r\n",
            array(),
            "BEGIN:VCARD\r\nVERSION:4.0\r\nFN:John Doe\r\nEND:VCARD\r\n",
        );

        // No VERSION
        $tests[] = array(
            "BEGIN:VCARD\r\nFN:John Doe\r\nEND:VCARD\r\n",
            array(
                'The VERSION property must appear in the VCARD component exactly 1 time',
            ),
            "BEGIN:VCARD\r\nVERSION:4.0\r\nFN:John Doe\r\nEND:VCARD\r\n",
        );

        // Unknown version
        $tests[] = array(
            "BEGIN:VCARD\r\nVERSION:2.2\r\nFN:John Doe\r\nEND:VCARD\r\n",
            array(
                'Only vcard version 4.0 (RFC6350), version 3.0 (RFC2426) or version 2.1 (icm-vcard-2.1) are supported.',
            ),
            "BEGIN:VCARD\r\nVERSION:4.0\r\nFN:John Doe\r\nEND:VCARD\r\n",
        );

        // No FN
        $tests[] = array(
            "BEGIN:VCARD\r\nVERSION:4.0\r\nEND:VCARD\r\n",
            array(
                'The FN property must appear in the VCARD component exactly 1 time',
            ),
            "BEGIN:VCARD\r\nVERSION:4.0\r\nEND:VCARD\r\n",
        );
        // No FN, N fallback
        $tests[] = array(
            "BEGIN:VCARD\r\nVERSION:4.0\r\nN:Doe;John;;;;;\r\nEND:VCARD\r\n",
            array(
                'The FN property must appear in the VCARD component exactly 1 time',
            ),
            "BEGIN:VCARD\r\nVERSION:4.0\r\nN:Doe;John;;;;;\r\nFN:John Doe\r\nEND:VCARD\r\n",
        );
        // No FN, N fallback, no first name
        $tests[] = array(
            "BEGIN:VCARD\r\nVERSION:4.0\r\nN:Doe;;;;;;\r\nEND:VCARD\r\n",
            array(
                'The FN property must appear in the VCARD component exactly 1 time',
            ),
            "BEGIN:VCARD\r\nVERSION:4.0\r\nN:Doe;;;;;;\r\nFN:Doe\r\nEND:VCARD\r\n",
        );

        // No FN, ORG fallback
        $tests[] = array(
            "BEGIN:VCARD\r\nVERSION:4.0\r\nORG:Acme Co.\r\nEND:VCARD\r\n",
            array(
                'The FN property must appear in the VCARD component exactly 1 time',
            ),
            "BEGIN:VCARD\r\nVERSION:4.0\r\nORG:Acme Co.\r\nFN:Acme Co.\r\nEND:VCARD\r\n",
        );
        return $tests;

    }

}
