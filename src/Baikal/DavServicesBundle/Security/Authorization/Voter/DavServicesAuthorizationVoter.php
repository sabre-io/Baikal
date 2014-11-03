<?php

namespace Baikal\DavServicesBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Exception\InvalidArgumentException,
    Symfony\Component\Security\Core\SecurityContext,
    Symfony\Component\Security\Core\Authorization\Voter\VoterInterface,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface,
    Symfony\Component\Security\Core\User\UserInterface;

use Baikal\ModelBundle\Entity\Calendar,
    Baikal\ModelBundle\Entity\Event,
    Baikal\ModelBundle\Entity\Addressbook,
    Baikal\ModelBundle\Entity\AddressbookContact,
    Baikal\CoreBundle\Services\MainConfigService;


class DavServicesAuthorizationVoter implements VoterInterface
{
    protected $container;
    protected $mainconfig;

    public function __construct($container, MainConfigService $mainconfig) {
        $this->container = $container;
        $this->mainconfig = $mainconfig;
    }

    public function supportsAttribute($attribute)
    {
        return in_array($attribute, array(
            'rest.api',     # is api enabled ?
            'dav.read',
            'dav.write',
            'dav.create',
            'dav.update',
            'dav.delete'
        ));
    }

    public function supportsClass($class) {
        $supportedClasses = array('Baikal\ModelBundle\Entity\Calendar', 'Baikal\ModelBundle\Entity\Addressbook');
        foreach($supportedClasses as $supportedClass) {
            if($supportedClass === $class || is_subclass_of($class, $supportedClass)) {
                return TRUE;
            }
        }

        return FALSE;
    }

    public function vote(TokenInterface $token, $calendar = null, array $attributes = array())
    {
        if(count($attributes) !== 1) {
            throw new InvalidArgumentException(
                'Only one attribute is allowed.'
            );
        }

        // set the attribute to check against
        $attribute = $attributes[0];

        if($attribute === 'rest.api') {
            # check if the REST service is enabled
            # we do that before supportsClass(), as in this case we have no $calendar to check against
            if($this->mainconfig->getEnable_api() !== TRUE) {
                return VoterInterface::ACCESS_DENIED;
            } else {
                return VoterInterface::ACCESS_GRANTED;
            }
        }

        # if not rest.api, we still check that the API is enabled
        if($this->mainconfig->getEnable_api() !== TRUE) {
            return VoterInterface::ACCESS_DENIED;
        }

        // check if class of this object is supported by this voter
        if (!$this->supportsClass(get_class($calendar))) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // get current logged in user
        $user = $token->getUser();

        // check if the given attribute is covered by this voter
        if (!$this->supportsAttribute($attribute)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return VoterInterface::ACCESS_DENIED;
        }

        if(
            $this->container->get('security.context')->isGranted('ROLE_ADMIN') ||
            $calendar->getPrincipaluri() === $user->getIdentityPrincipal()->getUri()
        ) {
            // read, write, create, update and delete
            return VoterInterface::ACCESS_GRANTED;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}