<?php

class Sabre_CalDAV_Backend_Mock extends Sabre_CalDAV_Backend_Abstract { 

    private $calendarData;

    function __construct($calendarData) {

        $this->calendarData = $calendarData;

    }

    /**
     * Returns a list of calendars for a principal.
     *
     * Every project is an array with the following keys:
     *  * id, a unique id that will be used by other functions to modify the
     *    calendar. This can be the same as the uri or a database key.
     *  * uri, which the basename of the uri with which the calendar is 
     *    accessed.
     *  * principalUri. The owner of the calendar. Almost always the same as
     *    principalUri passed to this method.
     *
     * Furthermore it can contain webdav properties in clark notation. A very
     * common one is '{DAV:}displayname'. 
     *
     * @param string $principalUri 
     * @return array 
     */
    function getCalendarsForUser($principalUri) {

        return array();

    }

    /**
     * Creates a new calendar for a principal.
     *
     * If the creation was a success, an id must be returned that can be used to reference
     * this calendar in other methods, such as updateCalendar.
     *
     * This function must return a server-wide unique id that can be used 
     * later to reference the calendar.
     *
     * @param string $principalUri
     * @param string $calendarUri
     * @param array $properties
     * @return string|int 
     */
    function createCalendar($principalUri,$calendarUri,array $properties) {

        throw new Exception('Not implemented');

    }

    /**
     * Updates properties on this node,
     *
     * The properties array uses the propertyName in clark-notation as key,
     * and the array value for the property value. In the case a property
     * should be deleted, the property value will be null.
     *
     * This method must be atomic. If one property cannot be changed, the
     * entire operation must fail.
     *
     * If the operation was successful, true can be returned.
     * If the operation failed, false can be returned.
     *
     * Deletion of a non-existant property is always succesful.
     *
     * Lastly, it is optional to return detailed information about any
     * failures. In this case an array should be returned with the following
     * structure:
     *
     * array(
     *   403 => array(
     *      '{DAV:}displayname' => null,
     *   ),
     *   424 => array(
     *      '{DAV:}owner' => null,
     *   )
     * )
     *
     * In this example it was forbidden to update {DAV:}displayname. 
     * (403 Forbidden), which in turn also caused {DAV:}owner to fail
     * (424 Failed Dependency) because the request needs to be atomic.
     *
     * @param string $calendarId
     * @param array $properties
     * @return bool|array 
     */
    public function updateCalendar($calendarId, array $properties) {
        
        return false; 

    }

    /**
     * Delete a calendar and all it's objects 
     * 
     * @param string $calendarId 
     * @return void
     */
    public function deleteCalendar($calendarId) {

        throw new Exception('Not implemented');

    }

    /**
     * Returns all calendar objects within a calendar object.
     *
     * Every item contains an array with the following keys:
     *   * id - unique identifier which will be used for subsequent updates
     *   * calendardata - The iCalendar-compatible calnedar data
     *   * uri - a unique key which will be used to construct the uri. This can be any arbitrary string.
     *   * lastmodified - a timestamp of the last modification time
     *   * etag - An arbitrary string, surrounded by double-quotes. (e.g.: 
     *   '  "abcdef"')
     *   * calendarid - The calendarid as it was passed to this function.
     *
     * Note that the etag is optional, but it's highly encouraged to return for 
     * speed reasons.
     *
     * The calendardata is also optional. If it's not returned 
     * 'getCalendarObject' will be called later, which *is* expected to return 
     * calendardata.
     * 
     * @param string $calendarId 
     * @return array 
     */
    public function getCalendarObjects($calendarId) {

        return $this->calendarData[$calendarId];

    }

    /**
     * Returns information from a single calendar object, based on it's object
     * uri.
     *
     * The returned array must have the same keys as getCalendarObjects. The 
     * 'calendardata' object is required here though, while it's not required 
     * for getCalendarObjects.
     * 
     * @param string $calendarId 
     * @param string $objectUri 
     * @return array 
     */
    function getCalendarObject($calendarId,$objectUri) {

        if (!isset($this->calendarData[$calendarId][$objectUri])) {
            throw new Sabre_DAV_Exception_FileNotFound('Object could not be found');
        }
        return $this->calendarData[$calendarId][$objectUri];

    }

    /**
     * Creates a new calendar object. 
     * 
     * @param string $calendarId 
     * @param string $objectUri 
     * @param string $calendarData 
     * @return void
     */
    function createCalendarObject($calendarId,$objectUri,$calendarData) {

        throw new Exception('Not implemented');

    }

    /**
     * Updates an existing calendarobject, based on it's uri. 
     * 
     * @param string $calendarId 
     * @param string $objectUri 
     * @param string $calendarData 
     * @return void
     */
    function updateCalendarObject($calendarId,$objectUri,$calendarData) {

        throw new Exception('Not implemented');

    }

    /**
     * Deletes an existing calendar object. 
     * 
     * @param string $calendarId 
     * @param string $objectUri 
     * @return void
     */
    function deleteCalendarObject($calendarId,$objectUri) {

        throw new Exception('Not implemented');


    }

}
