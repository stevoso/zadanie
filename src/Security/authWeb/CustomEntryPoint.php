<?php
namespace App\Security\authWeb;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class CustomEntryPoint implements AuthenticationEntryPointInterface {
    private RouterInterface $router;

    /**
     * konstruktor, kde parametre idu cez nastavenia v services.yml
     */
    public function __construct(RouterInterface $router){
        $this->router = $router;
    }

    public function start(Request $request, AuthenticationException $authException = null):RedirectResponse{
        $url = $this->router->generate('login');
        return new RedirectResponse($url);
    }

}
