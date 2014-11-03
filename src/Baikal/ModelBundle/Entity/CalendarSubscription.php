<?php

namespace Baikal\ModelBundle\Entity;

class CalendarSubscription
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
    private $principaluri;

    /**
     * @var string
     */
    private $source;

    /**
     * @var string
     */
    private $displayname;

    /**
     * @var string
     */
    private $refreshrate;

    /**
     * @var integer
     */
    private $calendarorder;

    /**
     * @var string
     */
    private $calendarcolor;

    /**
     * @var boolean
     */
    private $striptodos;

    /**
     * @var bool
     */
    private $stripalarms;

    /**
     * @var bool
     */
    private $stripattachments;

    /**
     * @var integer
     */
    private $lastmodified;

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
     * Get principaluri
     *
     * @return string 
     */
    public function getPrincipaluri()
    {
        return $this->principaluri;
    }

    /**
     * Set principaluri
     *
     * @param string $principaluri
     * @return CalendarSubscription
     */
    public function setPrincipaluri($principaluri)
    {
        $this->principaluri = $principaluri;
        return $this;
    }

    /**
     * Get source
     *
     * @return string 
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set source
     *
     * @param string $source
     * @return CalendarSubscription
     */
    public function setSource($source)
    {
        $this->source = $source;
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
     * Set displayname
     *
     * @param string $source
     * @return CalendarSubscription
     */
    public function setDisplayname($displayname)
    {
        $this->displayname = $displayname;
        return $this;
    }

    /**
     * Get refreshrate
     *
     * @return string 
     */
    public function getRefreshrate()
    {
        return $this->refreshrate;
    }

    /**
     * Set refreshrate
     *
     * @param string $refreshrate
     * @return CalendarSubscription
     */
    public function setRefreshrate($refreshrate)
    {
        $this->refreshrate = $refreshrate;
        return $this;
    }

    /**
     * Get calendarorder
     *
     * @return integer 
     */
    public function getCalendarorder()
    {
        return $this->calendarorder;
    }

    /**
     * Set calendarorder
     *
     * @param integer $calendarorder
     * @return CalendarSubscription
     */
    public function setCalendarorder($calendarorder)
    {
        $this->calendarorder = $calendarorder;
        return $this;
    }

    /**
     * Get calendarcolor
     *
     * @return string 
     */
    public function getCalendarcolor()
    {
        return $this->calendarcolor;
    }

    /**
     * Set calendarcolor
     *
     * @param string $calendarcolor
     * @return CalendarSubscription
     */
    public function setCalendarcolor($calendarcolor)
    {
        $this->calendarcolor = $calendarcolor;
        return $this;
    }

    /**
     * Get striptodos
     *
     * @return string 
     */
    public function getStriptodos()
    {
        return $this->striptodos;
    }

    /**
     * Set striptodos
     *
     * @param string $striptodos
     * @return CalendarSubscription
     */
    public function setStriptodos($striptodos)
    {
        $this->striptodos = $striptodos;
        return $this;
    }

    /**
     * Get stripalarms
     *
     * @return bool 
     */
    public function getStripalarms()
    {
        return $this->stripalarms;
    }

    /**
     * Set stripalarms
     *
     * @param bool $stripalarms
     * @return CalendarSubscription
     */
    public function setStripalarms($stripalarms)
    {
        $this->stripalarms = $stripalarms;
        return $this;
    }

    /**
     * Get stripattachments
     *
     * @return bool 
     */
    public function getStripattachements()
    {
        return $this->stripattachments;
    }

    /**
     * Set stripattachments
     *
     * @param bool $stripattachments
     * @return CalendarSubscription
     */
    public function setStripattachements($stripattachments)
    {
        $this->stripattachments = $stripattachments;
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
     * Set lastmodified
     *
     * @param integer $lastmodified
     * @return CalendarSubscription
     */
    public function setLastmodified($lastmodified)
    {
        $this->lastmodified = $lastmodified;
        return $this;
    }
}
