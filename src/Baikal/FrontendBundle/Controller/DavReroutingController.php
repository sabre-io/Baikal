<?php

namespace Baikal\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\RedirectResponse;

class DavReroutingController extends Controller {

    public function propfindAction(Request $request) {

        if(preg_match('%caldav%smix', $request->getContent())) {
            return $this->toCaldav();
        }
        
        if(preg_match('%carddav%smix', $request->getContent())) {
            return $this->toCarddav();
        }

        if(in_array($request->getMethod(), array('PROPFIND', 'OPTIONS', 'REPORT'))) {
            $useragent = trim($request->headers->get('User-Agent'));
            if(preg_match('/(CalendarAgent|CalendarStore|iCal|Cal)/', $useragent)) {
                return $this->toCaldav();
            }

            if(preg_match('/(AddressBook)/', $useragent)) {
                return $this->toCarddav();
            }
        }
        
        return new RedirectResponse($this->generateUrl("baikal_frontend_homepage"), 301); # 302
    }

    protected function toCaldav() {
        return new RedirectResponse($this->generateUrl("baikal_dav_services_caldav"), 301); # 302
    }

    protected function toCarddav() {
        return new RedirectResponse($this->generateUrl("baikal_dav_services_carddav"), 301); # 302
    }
}
