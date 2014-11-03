<?php

namespace Baikal\ModelBundle\Entity;

class Addressbook
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $principaluri;

    /**
     * @var string
     */
    private $displayname;

    /**
     * @var string
     */
    private $uri;

    /**
     * @var string
     */
    private $description;

    /**
     * @var integer
     */
    private $synctoken;

    private $contacts;

    private $changes;

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
     * Set principaluri
     *
     * @param string $principaluri
     * @return Addressbook
     */
    public function setPrincipaluri($principaluri)
    {
        $this->principaluri = $principaluri;

        return $this;
    }

    /**
     * Get principaluri
     *
     * @return string 
     */
    public function getPrincipaluri()
    {
        return $this->principaluri;
    }

    /**
     * Set displayname
     *
     * @param string $displayname
     * @return Addressbook
     */
    public function setDisplayname($displayname)
    {
        $this->displayname = $displayname;

        return $this;
    }

    /**
     * Get displayname
     *
     * @return string 
     */
    public function getDisplayname()
    {
        return $this->displayname;
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
     * Set description
     *
     * @param string $description
     * @return Addressbook
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
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
     * Get contacts
     *
     * @return string 
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * Get changes
     *
     * @return string 
     */
    public function getChanges()
    {
        return $this->changes;
    }
}
