<?php
namespace App\Controller\app\user;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    #[Route('/login', name: 'login')]
    public function loginAction(AuthenticationUtils $authUtils):RedirectResponse|Response{
        $user = $this->getUser();
        if($user instanceof UserInterface){
            return $this->redirectToRoute('welcome');
        }

        /** @var AuthenticationException $exception */
        $exception = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();
        return $this->render('webTemplates/user/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $exception?->getMessage()
        ]);
    }

    #[Route('/login_check', name: 'loginCheck')]
    public function loginCheckAction():Response{
        return $this->redirectToRoute('welcome');
    }

    // this empty method is required by Symfony Guard
    #[Route('/logout', name: 'logout')]
    public function logoutAction():Response{
        return $this->redirectToRoute('index');
    }

}
