<?php
namespace App\Controller\app\user;

use App\Form\user\registration\RegistrationType;
use App\Form\user\registration\RegistrationTypeData;
use App\Service\user\UserManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController{

    public function __construct(
        private UserManager $userManager
    ){ }

    #[Route('/registracia', name: 'registrationForm')]
    public function registrationForm(Request $request): Response {
        $registrationTypeData = new RegistrationTypeData();
        $form = $this->createForm(RegistrationType::class, $registrationTypeData);
        $form->handleRequest($request);

        $systemErrors = [];
        if($form->isSubmitted() && $form->isValid()){
            try{
                $this->userManager->createUser($registrationTypeData);
                return $this->redirectToRoute('registrationSuccess');
            }catch(Exception $e){
                $systemErrors[] = $e->getMessage();
            }
        }
        $formErrors = $form->getErrors(true);
        $twigData = [
            'form' => $form->createView(),
            'formErrors' => $formErrors,
            'systemErrors' => $systemErrors
        ];
        return $this->render('webTemplates/user/registration/registrationForm.html.twig', $twigData);
    }

    #[Route('/registration-success', name: 'registrationSuccess')]
    public function registrationSuccess(): Response {
        return $this->render('webTemplates/user/registration/registrationSuccess.html.twig');
    }

}
