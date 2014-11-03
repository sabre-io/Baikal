<?php

namespace Baikal\ModelBundle\Entity;

use Sabre\VObject;

class AddressbookContact
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $addressbook;

    /**
     * @var string
     */
    private $carddata;

    /**
     * @var string
     */
    private $uri;

    /**
     * @var integer
     */
    private $lastmodified;

    private $vobject;

    private $etag;

    private $size;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set addressbook
     *
     * @param Addressbook $addressbook
     * @return AddressbookContact
     */
    public function setAddressbook(Addressbook $addressbook)
    {
        $this->addressbook = $addressbook;

        return $this;
    }

    /**
     * Get addressbook
     *
     * @return integer 
     */
    public function getAddressbook()
    {
        return $this->addressbook;
    }

    /**
     * Set carddata
     *
     * @param string $carddata
     * @return AddressbookContact
     */
    public function setCarddata($carddata)
    {
        $this->carddata = $carddata;

        return $this;
    }

    /**
     * Get carddata
     *
     * @return string 
     */
    public function getCarddata()
    {
        return $this->carddata;
    }

    /**
     * Set uri
     *
     * @param string $uri
     * @return AddressbookContact
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Get uri
     *
     * @return string 
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Set lastmodified
     *
     * @param integer $lastmodified
     * @return AddressbookContact
     */
    public function setLastmodified($lastmodified)
    {
        $this->lastmodified = $lastmodified;

        return $this;
    }

    /**
     * Get lastmodified
     *
     * @return integer 
     */
    public function getLastmodified()
    {
        return $this->lastmodified;
    }

    /**
     * Set etag
     *
     * @param string $etag
     * @return AddressbookContact
     */
    public function setEtag($etag)
    {
        $this->etag = $etag;

        return $this;
    }

    /**
     * Get etag
     *
     * @return integer 
     */
    public function getEtag()
    {
        return $this->etag;
    }

    /**
     * Set size
     *
     * @param string $size
     * @return AddressbookContact
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return integer 
     */
    public function getSize()
    {
        return $this->size;
    }

    # Dav wrapper methods below this line

    public function getVObject() {
        if(is_null($this->vobject)) {
            $this->vobject = VObject\Reader::read($this->getCarddata());
        }

        return $this->vobject;
    }


}
