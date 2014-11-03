<?php

namespace Baikal\ModelBundle\Entity;

class CalendarChange
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
     * @var Calendar
     */
    private $calendar;

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
     * @return CalendarChange
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
     * @return CalendarChange
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
     * Get calendar
     *
     * @return Calendar
     */
    public function getCalendar()
    {
        return $this->calendar;
    }

    /**
     * Set calendar
     *
     * @return CalendarChange
     */
    public function setCalendar(Calendar $calendar)
    {
        $this->calendar = $calendar;
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
     * @return CalendarChange
     */
    public function setOperation($operation)
    {
        $this->operation = $operation;
        return $this;
    }
}
