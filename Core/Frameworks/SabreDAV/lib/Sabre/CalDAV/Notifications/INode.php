<?php

namespace Sabre\CalDAV\Notifications;

/**
 * This node represents a single notification.
 *
 * The signature is mostly identical to that of Sabre\DAV\IFile, but the get() method
 * MUST return an xml document that matches the requirements of the
 * 'caldav-notifications.txt' spec.
 *
 * For a complete example, check out the Notification class, which contains
 * some helper functions.
 *
 * @copyright Copyright (C) 2007-2012 Rooftop Solutions. All rights reserved.
 * @author Evert Pot (http://www.rooftopsolutions.nl/)
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 */
interface INode {

    /**
     * This method must return an xml element, using the
     * Sabre\CalDAV\Notifications\INotificationType classes.
     *
     * @return INotificationType
     */
    function getNotificationType();

    /**
     * Returns the etag for the notification.
     *
     * The etag must be surrounded by litteral double-quotes.
     *
     * @return string
     */
    function getETag();

}
