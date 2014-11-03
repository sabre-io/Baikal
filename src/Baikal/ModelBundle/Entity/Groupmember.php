<?php

namespace Baikal\ModelBundle\Entity;

class Groupmember
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var UserPrincipal
     */
    private $principal;

    /**
     * @var integer
     */
    private $member_id;

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
     * Set principal
     *
     * @param UserPrincipal $principal
     * @return Groupmember
     */
    public function setPrincipal(UserPrincipal $principal)
    {
        $this->principal = $principal;
        return $this;
    }

    /**
     * Get principal
     *
     * @return string 
     */
    public function getPrincipal()
    {
        return $this->principal;
    }

    /**
     * Set member_id
     *
     * @param integer $member_id
     * @return Groupmember
     */
    public function setMember_id($member_id)
    {
        $this->member_id = $member_id;
        return $this;
    }

    /**
     * Get member_id
     *
     * @return string 
     */
    public function getMember_id()
    {
        return $this->member_id;
    }
}
