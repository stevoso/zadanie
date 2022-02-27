<?php
namespace App\Controller\app\settings;

use App\Entity\rss\Channel;
use App\Entity\user\User;
use App\Form\rss\settings\RssChannelType;
use App\Form\rss\settings\RssChannelTypeData;
use App\Service\settings\RssChannelManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class RssChannelController extends AbstractController {

    public function __construct(
        private RssChannelManager $rssChannelManager,
    ){ }

    #[Route('/settings/rss-channel/listing', name: 'rssChannel_listing')]
    public function listing():Response{
        /**@var User $loggedUser */
        $loggedUser = $this->getUser();
        $rssChannels = $this->rssChannelManager->channelRepository->findBy(['idUser' => $loggedUser->getId()], ['title' => 'ASC']);

        return $this->render('webTemplates/settings/rssChannel/listing.html.twig', [
            'rssChannels' => $rssChannels,
        ]);
    }

    #[Route('/settings/rss-channel/edit/{id}', name: 'rssChannel_edit')]
    public function edit(int $id, Request $request):Response{
        /**@var Channel $rssChannel */
        $rssChannel = $this->rssChannelManager->channelRepository->find($id);
        if($rssChannel === null){
            throw new NotFoundHttpException('zaznam nenájdeny');
        }
        return $this->save($request, $rssChannel);
    }

    #[Route('/settings/rss-channel/new', name: 'rssChannel_new')]
    public function new(Request $request):Response{
        /**@var User $loggedUser */
        $loggedUser = $this->getUser();
        $rssChannel = new Channel();
        $rssChannel->setIdUser($loggedUser->getId());
        return $this->save($request, $rssChannel);
    }

    private function save(Request $request, Channel $rssChannel):Response{
        $typeData = new RssChannelTypeData();
        $typeData->setTitle($rssChannel->getTitle());
        $typeData->setSubtitle($rssChannel->getSubtitle());

        $form = $this->createForm(RssChannelType::class, $typeData);
        $form->handleRequest($request);
        $isEdit = !empty($rssChannel->getId());

        $formSubmitUrl = $this->generateUrl('rssChannel_new');
        if($isEdit){
            $formSubmitUrl = $this->generateUrl('rssChannel_edit', ['id' => $rssChannel->getId()]);
        }

        $systemErrors = [];
        if($form->isSubmitted() && $form->isValid()){
            try{
                $this->rssChannelManager->saveRssChannelFromType($rssChannel, $typeData);
                return $this->render('webTemplates/settings/rssChannel/closeDialogAndRefresh.html.twig');
            }catch(Exception $e){
                $systemErrors[] = $e->getMessage();
            }
        }
        // formular
        return $this->render('webTemplates/settings/rssChannel/form.html.twig', [
            'form' => $form->createView(),
            'formSubmitUrl' => $formSubmitUrl,
            'systemErrors' => $systemErrors
        ]);
    }

    #[Route('/settings/rss-channel/delete/{id}', name: 'rssChannel_delete')]
    public function delete(int $id): Response {
        /**@var Channel $rssChannel */
        $rssChannel = $this->rssChannelManager->channelRepository->find($id);
        if($rssChannel === null){
            throw new NotFoundHttpException('zaznam nenájdeny');
        }

        $this->rssChannelManager->deleteItems([$rssChannel]);
        return $this->render('webTemplates/settings/rssChannel/refreshListing.html.twig');
    }

}
