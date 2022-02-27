<?php
namespace App\Controller\app\settings;

use App\Entity\rss\EntryFlag;
use App\Entity\user\User;
use App\Form\rss\settings\RssEntryFlagType;
use App\Form\rss\settings\RssEntryFlagTypeData;
use App\Service\settings\EntryFlagManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class EntryFlagController extends AbstractController {

    public function __construct(
        private EntryFlagManager $entryFlagManager,
    ){ }

    #[Route('/settings/entry-flag/listing', name: 'entryFlag_listing')]
    public function listing():Response{
        /**@var User $loggedUser */
        $loggedUser = $this->getUser();
        $entryFlags = $this->entryFlagManager->entryFlagRepository->findBy(['idUser' => $loggedUser->getId()], ['name' => 'ASC']);

        return $this->render('webTemplates/settings/entryFlag/listing.html.twig', [
            'entryFlags' => $entryFlags,
        ]);
    }

    #[Route('/settings/entry-flag/edit/{id}', name: 'entryFlag_edit')]
    public function edit(int $id, Request $request):Response{
        /**@var EntryFlag $entryFlag */
        $entryFlag = $this->entryFlagManager->entryFlagRepository->find($id);
        if($entryFlag === null){
            throw new NotFoundHttpException('zaznam nenájdeny');
        }
        return $this->save($request, $entryFlag);
    }

    #[Route('/settings/entry-flag/new', name: 'entryFlag_new')]
    public function new(Request $request):Response{
        /**@var User $loggedUser */
        $loggedUser = $this->getUser();
        $entryFlag = new EntryFlag();
        $entryFlag->setIdUser($loggedUser->getId());
        return $this->save($request, $entryFlag);
    }

    private function save(Request $request, EntryFlag $entryFlag):Response{
        $typeData = new RssEntryFlagTypeData();
        $typeData->setName($entryFlag->getName());
        $typeData->setColor($entryFlag->getColor());

        $form = $this->createForm(RssEntryFlagType::class, $typeData, ['colors'=>EntryFlag::$colors]);
        $form->handleRequest($request);
        $isEdit = !empty($entryFlag->getId());

        $formSubmitUrl = $this->generateUrl('entryFlag_new');
        if($isEdit){
            $formSubmitUrl = $this->generateUrl('entryFlag_edit', ['id' => $entryFlag->getId()]);
        }

        $systemErrors = [];
        if($form->isSubmitted() && $form->isValid()){
            try{
                $this->entryFlagManager->saveEntryFlagFromType($entryFlag, $typeData);
                return $this->render('webTemplates/settings/entryFlag/closeDialogAndRefresh.html.twig');
            }catch(Exception $e){
                $systemErrors[] = $e->getMessage();
            }
        }
        // formular
        return $this->render('webTemplates/settings/entryFlag/form.html.twig', [
            'form' => $form->createView(),
            'formSubmitUrl' => $formSubmitUrl,
            'systemErrors' => $systemErrors
        ]);
    }

    #[Route('/settings/entry-flag/delete/{id}', name: 'entryFlag_delete')]
    public function delete(int $id): Response {
        /**@var EntryFlag $entryFlag */
        $entryFlag = $this->entryFlagManager->entryFlagRepository->find($id);
        if($entryFlag === null){
            throw new NotFoundHttpException('zaznam nenájdeny');
        }

        $this->entryFlagManager->deleteItems([$entryFlag]);
        return $this->render('webTemplates/settings/entryFlag/refreshListing.html.twig');
    }

}
