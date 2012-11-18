<?php

namespace Sabre\CalDAV\Notifications;
use Sabre\DAV;

/**
 * This interface reflects a single notification type.
 *
 * @copyright Copyright (C) 2007-2012 Rooftop Solutions. All rights reserved.
 * @author Evert Pot (http://www.rooftopsolutions.nl/)
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 */
interface INotificationType extends DAV\PropertyInterface {

    /**
     * This method serializes the entire notification, as it is used in the
     * response body.
     *
     * @param DAV\Server $server
     * @param \DOMElement $node
     * @return void
     */
    function serializeBody(DAV\Server $server, \DOMElement $node);

    /**
     * Returns a unique id for this notification
     *
     * This is just the base url. This should generally be some kind of unique
     * id.
     *
     * @return string
     */
    function getId();

    /**
     * Returns the ETag for this notification.
     *
     * The ETag must be surrounded by literal double-quotes.
     *
     * @return string
     */
    function getETag();

}
