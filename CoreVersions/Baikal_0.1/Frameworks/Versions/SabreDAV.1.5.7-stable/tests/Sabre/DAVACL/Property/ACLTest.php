<?php

class Sabre_DAVACL_Property_ACLTest extends PHPUnit_Framework_TestCase {

    function testConstruct() {

        $acl = new Sabre_DAVACL_Property_Acl(array());

    }

    function testSerializeEmpty() {

        $dom = new DOMDocument('1.0');
        $root = $dom->createElementNS('DAV:','d:root');
        
        $dom->appendChild($root);

        $acl = new Sabre_DAVACL_Property_Acl(array());
        $acl->serialize(new Sabre_DAV_Server(), $root);

        $xml = $dom->saveXML();
        $expected = '<?xml version="1.0"?>
<d:root xmlns:d="DAV:"/>
';
        $this->assertEquals($expected, $xml);

    }

    function testSerialize() {

        $dom = new DOMDocument('1.0');
        $root = $dom->createElementNS('DAV:','d:root');
        
        $dom->appendChild($root);

        $privileges = array(
            array(
                'principal' => 'principals/evert',
                'privilege' => '{DAV:}write',
                'uri'       => 'articles',
            ),
            array(
                'principal' => 'principals/foo',
                'privilege' => '{DAV:}read',
                'uri'       => 'articles',
                'protected' => true,
            ),
        );

        $acl = new Sabre_DAVACL_Property_Acl($privileges);
        $acl->serialize(new Sabre_DAV_Server(), $root);

        $dom->formatOutput = true;

        $xml = $dom->saveXML();
        $expected = '<?xml version="1.0"?>
<d:root xmlns:d="DAV:">
  <d:ace>
    <d:principal>
      <d:href>/principals/evert/</d:href>
    </d:principal>
    <d:grant>
      <d:privilege>
        <d:write/>
      </d:privilege>
    </d:grant>
  </d:ace>
  <d:ace>
    <d:principal>
      <d:href>/principals/foo/</d:href>
    </d:principal>
    <d:grant>
      <d:privilege>
        <d:read/>
      </d:privilege>
    </d:grant>
    <d:protected/>
  </d:ace>
</d:root>
';
        $this->assertEquals($expected, $xml);

    }

    function testUnserialize() {

        $source = '<?xml version="1.0"?>
<d:root xmlns:d="DAV:">
  <d:ace>
    <d:principal>
      <d:href>/principals/evert/</d:href>
    </d:principal>
    <d:grant>
      <d:privilege>
        <d:write/>
      </d:privilege>
    </d:grant>
  </d:ace>
  <d:ace>
    <d:principal>
      <d:href>/principals/foo/</d:href>
    </d:principal>
    <d:grant>
      <d:privilege>
        <d:read/>
      </d:privilege>
    </d:grant>
    <d:protected/>
  </d:ace>
</d:root>
';

        $dom = Sabre_DAV_XMLUtil::loadDOMDocument($source);
        $result = Sabre_DAVACL_Property_Acl::unserialize($dom->firstChild);

        $this->assertInstanceOf('Sabre_DAVACL_Property_Acl', $result);

        $expected = array(
            array(
                'principal' => '/principals/evert/',
                'protected' => false,
                'privilege' => '{DAV:}write',
            ),
            array(
                'principal' => '/principals/foo/',
                'protected' => true,
                'privilege' => '{DAV:}read',
            ),
        );

        $this->assertEquals($expected, $result->getPrivileges());


    }

    /**
     * @expectedException Sabre_DAV_Exception_BadRequest
     */
    function testUnserializeNoPrincipal() {

        $source = '<?xml version="1.0"?>
<d:root xmlns:d="DAV:">
  <d:ace>
    <d:grant>
      <d:privilege>
        <d:write/>
      </d:privilege>
    </d:grant>
  </d:ace>
</d:root>
';

        $dom = Sabre_DAV_XMLUtil::loadDOMDocument($source);
        Sabre_DAVACL_Property_Acl::unserialize($dom->firstChild);

    }

    /**
     * @expectedException Sabre_DAV_Exception_NotImplemented
     */
    function testUnserializeUnsupportedPrincipal() {

        $source = '<?xml version="1.0"?>
<d:root xmlns:d="DAV:">
  <d:ace>
    <d:grant>
      <d:privilege>
        <d:write/>
      </d:privilege>
    </d:grant>
    <d:principal><d:authenticated /></d:principal>  
  </d:ace>
</d:root>
';

        $dom = Sabre_DAV_XMLUtil::loadDOMDocument($source);
        Sabre_DAVACL_Property_Acl::unserialize($dom->firstChild);

    }

    /**
     * @expectedException Sabre_DAV_Exception_NotImplemented
     */
    function testUnserializeDeny() {

        $source = '<?xml version="1.0"?>
<d:root xmlns:d="DAV:">
  <d:ace>
    <d:deny>
      <d:privilege>
        <d:write/>
      </d:privilege>
    </d:deny>
    <d:principal><d:href>/principals/evert</d:href></d:principal>  
  </d:ace>
</d:root>
';

        $dom = Sabre_DAV_XMLUtil::loadDOMDocument($source);
        Sabre_DAVACL_Property_Acl::unserialize($dom->firstChild);
    }

    /**
     * @expectedException Sabre_DAV_Exception_BadRequest
     */
    function testUnserializeMissingPriv() {

        $source = '<?xml version="1.0"?>
<d:root xmlns:d="DAV:">
  <d:ace>
    <d:grant>
      <d:privilege />
    </d:grant>
    <d:principal><d:href>/principals/evert</d:href></d:principal>  
  </d:ace>
</d:root>
';

        $dom = Sabre_DAV_XMLUtil::loadDOMDocument($source);
        Sabre_DAVACL_Property_Acl::unserialize($dom->firstChild);

    }
}
