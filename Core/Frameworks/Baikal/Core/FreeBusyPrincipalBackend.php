<?php

namespace Baikal\Core;

final class FreeBusyPrincipalBackend extends \Sabre\DAVACL\PrincipalBackend\PDO {
    private array $temporaryProxyReadMembers = [];

    public function addTemporaryCalendarProxyRead(
        string $organizerPrincipal,
        string $attendeePrincipal
    ): void {
        $proxyPrincipal = $attendeePrincipal . '/calendar-proxy-read';
        $this->temporaryProxyReadMembers[$proxyPrincipal][$organizerPrincipal] = true;
    }

    public function clearTemporaryCalendarProxyRead(): void {
        $this->temporaryProxyReadMembers = [];
    }

    public function getGroupMemberSet($principal) {
        $members = parent::getGroupMemberSet($principal);

        if (isset($this->temporaryProxyReadMembers[$principal])) {
            $members = array_merge(
                $members,
                array_keys($this->temporaryProxyReadMembers[$principal])
            );
        }

        return array_values(array_unique($members));
    }

    public function getGroupMembership($principal) {
        $groups = parent::getGroupMembership($principal);

        foreach ($this->temporaryProxyReadMembers as $proxyPrincipal => $members) {
            if (isset($members[$principal])) {
                $groups[] = $proxyPrincipal;
            }
        }

        return array_values(array_unique($groups));
    }
}
