<?php

namespace Baikal\ModelBundle\Entity;

class Lock
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $owner;

    /**
     * @var integer
     */
    private $timeout;

    /**
     * @var integer
     */
    private $created;

    /**
     * @var text
     */
    private $token;

    /**
     * @var integer
     */
    private $scope;

    /**
     * @var integer
     */
    private $depth;

    /**
     * @var string
     */
    private $uri;

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
     * Set owner
     *
     * @param string $owner
     * @return Lock
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * Get owner
     *
     * @return string 
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set timeout
     *
     * @param int $timeout
     * @return Lock
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * Get timeout
     *
     * @return int 
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Set created
     *
     * @param int $created
     * @return Lock
     */
    public function setCreated($created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * Get created
     *
     * @return int 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return Lock
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set scope
     *
     * @param string $scope
     * @return Lock
     */
    public function setScope($scope)
    {
        $this->scope = $scope;
        return $this;
    }

    /**
     * Get scope
     *
     * @return string 
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * Set depth
     *
     * @param string $depth
     * @return Lock
     */
    public function setDepth($depth)
    {
        $this->depth = $depth;
        return $this;
    }

    /**
     * Get depth
     *
     * @return string 
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * Set uri
     *
     * @param string $uri
     * @return Lock
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
}
