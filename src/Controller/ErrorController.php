<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
{
    // 404 - page not found
    #[Route('/error-404', name: 'CError404')]
    public function notFoundAction(): Response {
        return $this->render('webTemplates/404.html.twig');
    }

    // 403 - access denied
    #[Route('/error-403', name: 'CError403')]
    public function accessDeniedAction(): Response {
        return $this->render('webTemplates/403.html.twig');
    }
}
