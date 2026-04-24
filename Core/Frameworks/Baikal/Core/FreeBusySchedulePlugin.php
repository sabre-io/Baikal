<?php

namespace Baikal\Core;

use Sabre\CalDAV\Schedule\IOutbox;
use Sabre\CalDAV\Schedule\Plugin;
use Sabre\HTTP\RequestInterface;
use Sabre\HTTP\ResponseInterface;
use Sabre\VObject\Component;
use Sabre\VObject\Reader;

final class FreeBusySchedulePlugin extends Plugin {
    public function __construct(
        private FreeBusyPrincipalBackend $principalBackend,
        private bool $allowFreeBusyLookup = true
    ) {
    }

    public function outboxRequest(
        IOutbox $outboxNode,
        RequestInterface $request,
        ResponseInterface $response
    ) {
        if (!$this->allowFreeBusyLookup) {
            return parent::outboxRequest($outboxNode, $request, $response);
        }

        $body = $request->getBody();
        $vObject = Reader::read($body);
        // Reset body so the parent can reuse it
        $request->setBody($body);

        try {
            if (!$this->isFreeBusyRequest($vObject)) {
                return parent::outboxRequest($outboxNode, $request, $response);
            }

            $organizerPrincipal = $outboxNode->getOwner();

            foreach ($this->getAttendeePrincipals($vObject) as $attendeePrincipal) {
                $this->principalBackend->addTemporaryCalendarProxyRead(
                    $organizerPrincipal,
                    $attendeePrincipal
                );
            }

            return parent::outboxRequest($outboxNode, $request, $response);
        } finally {
            $this->principalBackend->clearTemporaryCalendarProxyRead();
            $vObject->destroy();
        }
    }

    private function isFreeBusyRequest(Component $vObject): bool {
        return isset($vObject->METHOD)
            && strtoupper((string) $vObject->METHOD) === 'REQUEST'
            && isset($vObject->VFREEBUSY);
    }

    private function getAttendeePrincipals(Component $vObject): array {
        $principals = [];

        if (!isset($vObject->VFREEBUSY->ATTENDEE)) {
            return [];
        }

        foreach ($vObject->VFREEBUSY->ATTENDEE as $attendee) {
            $uri = (string) $attendee;

            $principal = $this->principalBackend->findByUri($uri, 'principals');
            if ($principal !== null) {
                $principals[] = $principal;
            }
        }

        return array_values(array_unique($principals));
    }
}
