<?php
namespace App\Controller\app;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController{

    public function __construct(){
    }

    #[Route('/', name: 'index')]
    public function indexAction():RedirectResponse{
        return $this->redirectToRoute('login');
    }

    #[Route('/welcome', name: 'welcome')]
    public function welcome():RedirectResponse{
        return $this->redirectToRoute('rssEntry_listing');
    }

    /**
     * helper method: header of the application
     */
    public function headerAction():Response{
        return $this->render('webTemplates/header.html.twig');
    }

}
