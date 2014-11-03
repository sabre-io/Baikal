<?php

namespace Baikal\ModelBundle\Entity;

class AddressbookChange
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $uri;

    /**
     * @var integer
     */
    private $synctoken;

    /**
     * @var Addressbook
     */
    private $addressbook;

    /**
     * @var operation
     */
    private $operation;

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
     * Set uri
     *
     * @param string $uri
     * @return Addressbook
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
     * Set synctoken
     *
     * @param integer $synctoken
     * @return Addressbook
     */
    public function setSynctoken($synctoken)
    {
        $this->synctoken = $synctoken;

        return $this;
    }

    /**
     * Get synctoken
     *
     * @return integer 
     */
    public function getSynctoken()
    {
        return $this->synctoken;
    }

    /**
     * Get addressbook
     *
     * @return Addressbook
     */
    public function getAddressbook()
    {
        return $this->addressbook;
    }

    /**
     * Set addressbook
     *
     * @return AddressbookChange
     */
    public function setAddressbook(Addressbook $addressbook)
    {
        $this->addressbook = $addressbook;
        return $this;
    }

    /**
     * Get operation
     *
     * @return boolean
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * Set operation
     *
     * @return AddressbookChange
     */
    public function setOperation($operation)
    {
        $this->operation = $operation;
        return $this;
    }
}
