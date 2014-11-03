<?php

namespace Baikal\RestBundle\Serializer;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface,
    JMS\Serializer\EventDispatcher\ObjectEvent,
    JMS\Serializer\EventDispatcher\Events as SerializerEvents;

use Sabre\VObject;

use Baikal\CoreBundle\Services\MainConfigService;

class AddressbookContactSerializationSubscriber implements EventSubscriberInterface {
    
    protected $mainconfig;

    public function __construct(MainConfigService $mainconfig) {
        $this->mainconfig = $mainconfig;
    }

    public static function getSubscribedEvents() {
        return array(
            array(
                'event' => SerializerEvents::POST_SERIALIZE,
                'class' => 'Baikal\ModelBundle\Entity\AddressbookContact',
                'method' => 'onPostSerialize_AddressbookContact'
            ),
        );
    }

    public function onPostSerialize_AddressbookContact(ObjectEvent $event) {

        $contact = $event->getObject();
        $addressbook = $contact->getAddressbook();

        $vobject = $contact->getVObject();

        $event->getVisitor()->addData(
            'addressbook',
            $addressbook->getId()
        );

        /*
            CardDAV properties to serialize

            * ADR                 A structured representation of the physical delivery address for the vCard object.
            * AGENT               Information about another person who will act on behalf of the vCard object. Typically this would be an area administrator, assistant, or secretary for the individual. Can be either a URL or an embedded vCard.
            * ANNIVERSARY         Defines the person's anniversary.
            * BDAY                Date of birth of the individual associated with the vCard.
            * CALADRURI           A URL to use for sending a scheduling request to the person's calendar.
            * CALURI              A URL to the person's calendar.
            * CATEGORIES          A list of "tags" that can be used to describe the object represented by this vCard.
            * CLASS               Describes the sensitivity of the information in the vCard.
            * CLIENTPIDMAP        Used for synchronizing different revisions of the same vCard.
            * EMAIL               The address for electronic mail communication with the vCard object.
            * FBURL               Defines a URL that shows when the person is "free" or "busy" on their calendar.
            * FN                  The formatted name string associated with the vCard object.
            * GENDER              Defines the person's gender.
            * GEO                 Specifies a latitude and longitude.
            * IMPP                Defines an instant messenger handle.
            * KEY                 The public encryption key associated with the vCard object. It may point to an external URL, may be plain text, or may be embedded in the vCard as a Base64 encoded block of text.
            * KIND                Defines the type of entity that this vCard represents: 'application', 'individual, 'group', 'location' or 'organization'; 'x-*' values may be used for experimental purposes. cf. http://tools.ietf.org/html/rfc6350#section-6.1.4, http://tools.ietf.org/html/rfc6473 ('application' value)
            * LABEL               Represents the actual text that should be put on the mailing label when delivering a physical package to the person/object associated with the vCard (related to the ADR property).
            * LANG                Defines a language that the person speaks.
            * LOGO                An image or graphic of the logo of the organization that is associated with the individual to which the vCard belongs. It may point to an external URL or may be embedded in the vCard as a Base64 encoded block of text.
            * MAILER              Type of email program used.
            * MEMBER              Defines a member that is part of the group that this vCard represents.
            * N                   A structured representation of the name of the person, place or thing associated with the vCard object.
            * NAME                Provides a textual representation of the SOURCE property.
            * NICKNAME            One or more descriptive/familiar names for the object represented by this vCard.
            * NOTE                Specifies supplemental information or a comment that is associated with the vCard.
            * ORG                 The name and optionally the unit(s) of the organization associated with the vCard object. This property is based on the X.520 Organization Name attribute and the X.520 Organization Unit attribute.
            * PHOTO               An image or photograph of the individual associated with the vCard. It may point to an external URL or may be embedded in the vCard as a Base64 encoded block of text.
            * PRODID              The identifier for the product that created the vCard object.
            * PROFILE             States that the vCard is a vCard.
            * RELATED             Another entity that the person is related to.
            * REV                 A timestamp for the last time the vCard was updated.
            * ROLE                The role, occupation, or business category of the vCard object within an organization.
            * SORT-STRING         Defines a string that should be used when an application sorts this vCard in some way.
            * SOUND               By default, if this property is not grouped with other properties it specifies the pronunciation of the FN property of the vCard object. It may point to an external URL or may be embedded in the vCard as a Base64 encoded block of text.
            * SOURCE              A URL that can be used to get the latest version of this vCard.
            * TEL                 The canonical number string for a telephone number for telephony communication with the vCard object.
            * TITLE               Specifies the job title, functional position or function of the individual associated with the vCard object within an organization.
            * TZ                  The time zone of the vCard object.
            * UID                 Specifies a value that represents a persistent, globally unique identifier associated with the object.
            * URL                 A URL pointing to a website that represents the person in some way.
            * VERSION             The version of the vCard specification. In versions 3.0 and 4.0, this must come right after the BEGIN property.
            * XML                 Any XML data that is attached to the vCard. This is used if the vCard was encoded in XML (xCard standard) and the XML document contained elements which are not part of the xCard standard. XML:<b>Not an xCard XML element</b>

            * BIRTHPLACE          (RFC 6474) The location of the individual's birth.
            * DEATHDATE           (RFC 6474) The individual's time of death.
            * DEATHPLACE          (RFC 6474) The location of the individual death.
            * EXPERTISE           (RFC 6715) A professional subject area that the person has knowledge of.
            * HOBBY               (RFC 6715) A recreational activity that the person actively engages in.
            * IMPP                (RFC 4770) Defines an instant messenger handle. This was added to the official vCard specification in version 4.0.
            * INTEREST            (RFC 6715) A recreational activity that the person is interested in, but does not necessarily take part in.
            * ORG-DIRECTORY       (RFC 6715) A URI representing the person's work place, which can be used to lookup information on the person's co-workers.
        */

        $serializedproperties = array();
        
        $addToArraySerialized = function($name, $value) use (&$serializedproperties) {
            if(!array_key_exists($name, $serializedproperties)) { $serializedproperties[$name] = array(); }
            $serializedproperties[$name][] = $value;
        };

        $setScalarSerialized = function($name, $value) use (&$serializedproperties) {
            $serializedproperties[$name] = $value;
        };

        $addMultipleField = function($vobject, $name, $cbk = null) use (&$addToArraySerialized) {

            if(is_null($cbk)) { $cbk = function($vobject, $item) { return $item;}; }
            
            if($vobject->$name) {
                foreach($vobject->$name as $child) {
                    $addToArraySerialized($name, $cbk($vobject, $child->getValue()));
                }
            }
        };

        $addTypedField = function($vobject, $name, $cbk = null) use (&$addToArraySerialized) {

            if(is_null($cbk)) { $cbk = function($vobject, $item) { return $item;}; }
            
            if($vobject->$name) {
                foreach($vobject->$name as $child) {

                    $prefered = FALSE;

                    if(@$child['TYPE']) {
                        $types = $child['TYPE']->getParts();
                        if(in_array('pref', $types)) {
                            $prefered = TRUE;
                            unset($types[array_search('pref', $types)]);
                        }
                    } else {
                        $types = null;
                    }

                    $addToArraySerialized($name, $cbk($child, array(
                        'value' => $child->getValue(),
                        'prefered' => $prefered,
                        'type' => $types,
                    )));
                }
            }
        };

        #
        # N (Name)
        #
        if($vobject->N) {
            // @see https://tools.ietf.org/html/rfc6350#section-6.2.2
            // A structured representation of the name of the person, place or thing associated with the vCard object.
                // 0: Last names
                // 1: First names
                // 2: Additional names
                // 3: Honorific Prefixes
                // 4: Honorific Suffixes

            $parts = $vobject->N->getParts();
            $serializedproperties['N'] = array(
                'lastname' => $parts[0],
                'firstname' => $parts[1],
                'additionalname' => $parts[2],
                'honorificprefix' => $parts[3],
                'honorificsuffix' => $parts[4],
            );
        }

        #
        # FN (Full name)
        #

        if($vobject->FN) {
            $setScalarSerialized('FN', $vobject->FN->getValue());
        }

        #
        # EMAIL
        #

        $addTypedField($vobject, 'EMAIL');

        #
        # TEL
        #

        $addTypedField($vobject, 'TEL');

        #
        # ORG
        #

        if($vobject->ORG) {
            // @see https://tools.ietf.org/html/rfc6350#section-6.6.4
            // The name and optionally the unit(s) of the organization associated with the vCard object. This property is based on the X.520 Organization Name attribute and the X.520 Organization Unit attribute.

            $parts = $vobject->ORG->getParts();
            $org = array(
                'name' => $parts[0],
                'units' => array(),
            );

            for($i = 1; $i < count($parts); $i++) {
                if(trim($parts[$i]) !== '') {
                    $org['units'][] = $parts[$i];
                }
            }

            $serializedproperties['ORG'] = $org;
        }

        #
        # TITLE
        #

        if($vobject->TITLE) {
            $setScalarSerialized('TITLE', $vobject->TITLE->getJsonValue()[0]);
        }
        
        #
        # URL
        #

        if($vobject->URL) {

            $urls = array();

            foreach($vobject->URL as $url) {
                $parts = $url->getParts();
                $prefered = FALSE;

                if(@$url['TYPE']) {
                    $types = $url['TYPE']->getParts();
                    if(in_array('pref', $types)) {
                        $prefered = TRUE;
                        unset($types[array_search('pref', $types)]);
                    }
                } else {
                    $types = null;
                }

                $urls[] = array(
                    'value' => $url->getValue(),
                    'prefered' => $prefered,
                    'type' => $types
                );
            }

            $setScalarSerialized('URL', $urls);
        }

        #
        # BDAY
        #

        if($vobject->BDAY) {
            $setScalarSerialized('BDAY', $vobject->BDAY->getJsonValue()[0]);
        }

        #
        # IMPP
        #

        $addTypedField($vobject, 'IMPP');

        #
        # NOTE
        #

        $addMultipleField($vobject, 'NOTE');

        #
        # ADDR
        #

        $addTypedField($vobject, 'ADR', function($vobject, $item) {
            $parts = $vobject->getParts();

            return array(
                'postofficebox' => $parts[0],
                'extendedaddress' => $parts[1],
                'streetaddress' => $parts[2],
                'city' => $parts[3],
                'region' => $parts[4],
                'postalcode' => $parts[5],
                'country' => $parts[6],
                'prefered' => $item['prefered'],
                'type' => $item['type'],
            );
        });

        #
        # PHOTO
        #

        if($vobject->PHOTO) {

            $photo = array();

            if(@$vobject->PHOTO['TYPE']) {
                $photo['type'] = $vobject->PHOTO['TYPE']->getValue();
            }

            if(@$vobject->PHOTO['ENCODING']) {
                $photo['encoding'] = $vobject->PHOTO['encoding']->getValue();
            }

            $photo['value'] = $vobject->PHOTO->getJsonValue();

            $setScalarSerialized('PHOTO', $photo);
        }

        #
        # Company ?
        #

        $index = 'X-ABSHOWAS';
        $company = FALSE;

        if($vobject->$index && strtoupper($vobject->$index->getValue()) === 'COMPANY') {
            $company = TRUE;
        }

        $event->getVisitor()->addData('company', $company);
        
        foreach($serializedproperties as $propname => $propvalue) {
            $event->getVisitor()->addData(
                strtolower($propname),
                $propvalue
            );
        }
    }
}