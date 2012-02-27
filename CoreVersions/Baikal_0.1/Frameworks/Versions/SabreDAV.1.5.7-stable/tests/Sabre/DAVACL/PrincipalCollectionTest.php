<?php

require_once 'Sabre/DAVACL/MockPrincipalBackend.php';

class Sabre_DAVACL_PrincipalCollectionTest extends PHPUnit_Framework_TestCase {

    public function testBasic() {

        $backend = new Sabre_DAVACL_MockPrincipalBackend();
        $pc = new Sabre_DAVACL_PrincipalCollection($backend);
        $this->assertTrue($pc instanceof Sabre_DAVACL_PrincipalCollection);

        $this->assertEquals('principals',$pc->getName());

    }

    /**
     * @depends testBasic
     */
    public function testGetChildren() {

        $backend = new Sabre_DAVACL_MockPrincipalBackend();
        $pc = new Sabre_DAVACL_PrincipalCollection($backend);
        
        $children = $pc->getChildren();
        $this->assertTrue(is_array($children));

        foreach($children as $child) {
            $this->assertTrue($child instanceof Sabre_DAVACL_IPrincipal);
        }

    }

    /**
     * @depends testBasic
     * @expectedException Sabre_DAV_Exception_MethodNotAllowed
     */
    public function testGetChildrenDisable() {

        $backend = new Sabre_DAVACL_MockPrincipalBackend();
        $pc = new Sabre_DAVACL_PrincipalCollection($backend);
        $pc->disableListing = true;
        
        $children = $pc->getChildren();

    }

}
