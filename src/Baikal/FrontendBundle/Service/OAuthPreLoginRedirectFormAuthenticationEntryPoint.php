<?php

namespace Baikal\FrontendBundle\Service;

use Symfony\Component\Security\Http\EntryPoint\FormAuthenticationEntryPoint,
    Symfony\Component\Security\Core\Exception\AuthenticationException;

class OAuthPreLoginRedirectFormAuthenticationEntryPoint extends FormAuthenticationEntryPoint {

    public function start(Request $request, AuthenticationException $authException = null) {
        die('laaaaaa');
    }
}
