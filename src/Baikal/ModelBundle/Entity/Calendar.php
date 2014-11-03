<?php

namespace Baikal\ModelBundle\Entity;

class Calendar
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
     * @var integer
     */
    private $synctoken = 0;

    /**
     * @var string
     */
    private $description = '';

    /**
     * @var integer
     */
    private $calendarorder = 0;

    /**
     * @var string
     */
    private $calendarcolor = '#FF0000';

    /**
     * @var string
     */
    private $timezone = '';

    /**
     * @var string
     */
    private $components = 'VEVENT';

    /**
     * @var string
     */
    private $transparent = FALSE;

    private $events;

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
     * @return Calendar
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
     * @return Calendar
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
     * @return Calendar
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
     * @return Calendar
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
     * Set description
     *
     * @param string $description
     * @return Calendar
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
     * Set calendarorder
     *
     * @param integer $calendarorder
     * @return Calendar
     */
    public function setCalendarorder($calendarorder)
    {
        $this->calendarorder = $calendarorder;

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
     * Set calendarcolor
     *
     * @param string $calendarcolor
     * @return Calendar
     */
    public function setCalendarcolor($calendarcolor)
    {
        $this->calendarcolor = $calendarcolor;

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

    public function getCalendarColorAsHexRGB() {
        $color = $this->getCalendarcolor();
        if(preg_match('/^\#[a-f0-9]{8}$/i', $color)) {
            return substr($color, 0, 7);
        }

        return $color;
    }

    public function getTextColorAsHexRGB() {
        $hexcolor = $this->getCalendarColorAsHexRGB();
        $hexcolor = ltrim($hexcolor, '#');
        $r = hexdec(substr($hexcolor,0,2));
        $g = hexdec(substr($hexcolor,2,2));
        $b = hexdec(substr($hexcolor,4,2));
        $yiq = (($r*299)+($g*587)+($b*114))/1000;
        return ($yiq >= 128) ? '#000000' : '#ffffff';
    }

    /**
     * Set timezone
     *
     * @param string $timezone
     * @return Calendar
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Get timezone
     *
     * @return string 
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * Set components
     *
     * @param string $components
     * @return Calendar
     */
    public function setComponents($components)
    {
        $this->components = $components;

        return $this;
    }

    /**
     * Get components
     *
     * @return string 
     */
    public function getComponents()
    {
        return $this->components;
    }

    public function getTodos() {
        $components = explode(',', $this->getComponents());
        return in_array('VTODO', $components);
    }

    public function setTodos($todo) {
        $components = explode(',', $this->getComponents());

        if($todo) {
            if(!$this->getTodos()) {
                $components[] = 'VTODO';
            }
        } else {
            if($this->getTodos()) {
                unset($components[array_search('VTODO', $components)]);
            }
        }

        $this->setComponents(implode(',', $components));
        return $this;
    }

    /**
     * Set transparent
     *
     * @param string $transparent
     * @return Calendar
     */
    public function setTransparent($transparent)
    {
        $this->transparent = $transparent;

        return $this;
    }

    /**
     * Get transparent
     *
     * @return string 
     */
    public function getTransparent()
    {
        return $this->transparent;
    }

    public function getEvents() {
        return $this->events;
    }
}
