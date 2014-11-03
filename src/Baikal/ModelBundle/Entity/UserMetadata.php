<?php

namespace Baikal\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection,
    Symfony\Component\Validator\Constraints as Assert,
    Symfony\Bridge\Doctrine\Validator\Constraints as ORMAssert;

use Baikal\ModelBundle\Entity\User,
    Baikal\ModelBundle\Entity\Addressbook;

/**
 * UserMetadata; to use SabreDav user as a Baïkal User, while allowing the user extension with Baïkal's specific metadata
 */
class UserMetadata {

    /**
     * @var integer
     */
    private $id;

    /**
     * @var User
     * # This is the owning side of the 1-1 relationship
     */
    private $user;

    /**
     * @var array
     * @Assert\NotBlank(message="Veuillez renseigner au moins un rôle.")
     */
    private $roles;

    public function __construct() {
        $this->roles = array();
    }

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
     * Get user
     *
     * @return User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set user
     *
     * @return UserMetadata
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRoles() {
        return $this->roles;
    }

     /**
     * @inheritDoc
     */
    public function setRoles(array $roles) {
        return $this->roles = $roles;
    }

    /**
     * @inheritDoc
     */
    public function addRole($role) {

        $role = strtoupper($role);
        if ($role === "ROLE_DEFAULT") {
            return;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
    }

    /**
     * @inheritDoc
     */
    public function removeRole($role) {

        $role = strtoupper($role);
        if ($role === "ROLE_DEFAULT") {
            return;
        }

        if (in_array($role, $this->roles, true)) {
            unset($this->roles[array_search($role, $this->roles)]);
        }
    }
}