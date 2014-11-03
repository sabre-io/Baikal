<?php

namespace Baikal\ModelBundle\Entity;

class UserPrincipal
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
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $displayname;

    /**
     * @var string
     */
    private $vcardurl;

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
     * @return UserPrincipal
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
     * Set email
     *
     * @param string $email
     * @return UserPrincipal
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set displayname
     *
     * @param string $displayname
     * @return UserPrincipal
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
     * Set vcardurl
     *
     * @param string $vcardurl
     * @return UserPrincipal
     */
    public function setVcardurl($vcardurl)
    {
        $this->vcardurl = $vcardurl;

        return $this;
    }

    /**
     * Get vcardurl
     *
     * @return string 
     */
    public function getVcardurl()
    {
        return $this->vcardurl;
    }
}
