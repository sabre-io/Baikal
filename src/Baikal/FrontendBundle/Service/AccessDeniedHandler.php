<?php

namespace Baikal\FrontendBundle\Service;

use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

# Interfaces for the injected services
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Routing\RouterInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface {

    private $kernel;
    private $securityContext;
    private $router;

    public function __construct(HttpKernelInterface $http_kernel, SecurityContextInterface $securityContext, RouterInterface $router) {
        $this->kernel = $http_kernel;
        $this->securityContext = $securityContext;
        $this->router = $router;
    }

    /**
     * Handles an access denied failure.
     *
     * @param Request               $request
     * @param AccessDeniedException $accessDeniedException
     *
     * @return Response may return null
     */
    public function handle(Request $request, AccessDeniedException $accessDeniedException) {

        # First we check if user has an access granted to the frontend
        if($this->securityContext->isGranted('ROLE_ADMIN') || $this->securityContext->isGranted('ROLE_STATIC_ADMIN')) {
            return new RedirectResponse($this->router->generate('baikal_admin_homepage'));
        }

        # If not, we display an "Access denied" message
        $attributes = array(
            '_controller' => 'BaikalCoreBundle:Security:accessDenied',
            'exception' => $accessDeniedException,
        );

        $subRequest = $request->duplicate(array(), null, $attributes);
        return $this->kernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }
}