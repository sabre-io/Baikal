<?php

namespace Baikal\ModelBundle\Entity;

use Baikal\ModelBundle\Entity\Calendar;

use Sabre\VObject;

class Event
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $calendardata;

    /**
     * @var string
     */
    private $uri;

    /**
     * @var integer
     */
    private $lastmodified;

    /**
     * @var string
     */
    private $etag;

    /**
     * @var integer
     */
    private $size;

    /**
     * @var string
     */
    private $componenttype;

    /**
     * @var integer
     */
    private $firstoccurence;

    /**
     * @var integer
     */
    private $lastoccurence;

    private $calendar;

    private $vobject;


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
     * Set calendardata
     *
     * @param string $calendardata
     * @return Event
     */
    public function setCalendardata($calendardata)
    {
        $this->calendardata = $calendardata;

        return $this;
    }

    /**
     * Get calendardata
     *
     * @return string 
     */
    public function getCalendardata()
    {
        return $this->calendardata;
    }

    /**
     * Set uri
     *
     * @param string $uri
     * @return Event
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
     * Set calendar
     *
     * @param Calendar $calendar
     * @return Event
     */
    public function setCalendar(Calendar $calendar)
    {
        $this->calendar = $calendar;
        return $this;
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
     * Set lastmodified
     *
     * @param integer $lastmodified
     * @return Event
     */
    public function setLastmodified($lastmodified)
    {
        $this->lastmodified = $lastmodified;

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
     * Get lastmodified
     *
     * @return integer 
     */
    public function getLastmodifiedAsDateTime()
    {
        $datetime = new \DateTime();
        $datetime->setTimestamp($this->getLastmodified());
        return $datetime;
    }

    /**
     * Set etag
     *
     * @param string $etag
     * @return Event
     */
    public function setEtag($etag)
    {
        $this->etag = $etag;

        return $this;
    }

    /**
     * Get etag
     *
     * @return string 
     */
    public function getEtag()
    {
        return $this->etag;
    }

    /**
     * Set size
     *
     * @param integer $size
     * @return Event
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return integer 
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set componenttype
     *
     * @param string $componenttype
     * @return Event
     */
    public function setComponenttype($componenttype)
    {
        $this->componenttype = $componenttype;

        return $this;
    }

    /**
     * Get componenttype
     *
     * @return string 
     */
    public function getComponenttype()
    {
        return $this->componenttype;
    }

    /**
     * Set firstoccurence
     *
     * @param integer $firstoccurence
     * @return Event
     */
    public function setFirstoccurence($firstoccurence)
    {
        $this->firstoccurence = $firstoccurence;

        return $this;
    }

    /**
     * Get firstoccurence
     *
     * @return integer 
     */
    public function getFirstoccurence()
    {
        return $this->firstoccurence;
    }

    /**
     * Set lastoccurence
     *
     * @param integer $lastoccurence
     * @return Event
     */
    public function setLastoccurence($lastoccurence)
    {
        $this->lastoccurence = $lastoccurence;

        return $this;
    }

    /**
     * Get lastoccurence
     *
     * @return integer 
     */
    public function getLastoccurence()
    {
        return $this->lastoccurence;
    }

    # Dav wrapper methods below this line

    public function getVObject() {
        if(is_null($this->vobject)) {
            
            if(empty($this->getCalendardata())) {
                $this->vobject = new VObject\Component\VCalendar();
                $this->vobject->add('VEVENT', [
                    'SUMMARY' => 'Empty',
                    'DTSTART' => new \DateTime(),
                    'DTEND' => new \DateTime(),
                ]);
                $this->setCalendardata($this->vobject->serialize());
            }

            $this->vobject = VObject\Reader::read($this->getCalendardata());
        }

        return $this->vobject;
    }

    #public function setVObject(VObject\Component\VCalendar $vobject) {
    #    $this->vobject = $vobject;
    #    $this->updateVObject();
    #}

    #protected function updateVObject() {
        #$this->calendardata = $this->getVObject()->serialize();
        #$this->setSize(mb_strlen($this->getCalendardata(), 'UTF-8'));
        #$this->setEtag(md5($this->getCalendardata()));
        #$this->setLastmodified(time());
        #$this->setFirstoccurence($this->getVObject()->VEVENT->DTSTART->getDateTime()->getTimeStamp());
        #$this->setLastoccurence($this->getVObject()->VEVENT->DTEND->getDateTime()->getTimeStamp());
    #}

}
