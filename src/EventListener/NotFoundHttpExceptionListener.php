<?php
namespace App\EventListener;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NotFoundHttpExceptionListener
{
    private Router $router;

    public function __construct(Router $router){
        $this->router = $router;
    }

    public function onKernelException(ExceptionEvent $event){
        $exception = $event->getThrowable();

        // 404 - NotFoundHttpException
        if ($exception instanceof NotFoundHttpException) {
            $url = $this->router->generate('CError404');
            $response = new RedirectResponse($url);
            $event->setResponse($response);
        }

        // 403 - AccessDeniedHttpException
        if ($exception instanceof AccessDeniedHttpException) {
            $url = $this->router->generate('CError403');
            $response = new RedirectResponse($url);
            $event->setResponse($response);
        }
    }
}
