<?php
namespace App\Controller\app\rssEntry;

use App\Entity\rss\Entry;
use App\Entity\rss\EntryUserStatus;
use App\Entity\user\User;
use App\Form\rss\RssEntryType;
use App\Form\rss\RssEntryTypeData;
use App\Service\entry\RssEntryManager;
use App\Service\settings\EntryFlagManager;
use App\Service\settings\RssChannelManager;
use App\Service\user\UserManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class EntryController extends AbstractController {

    public function __construct(
        private RssEntryManager $rssEntryManager,
        private RssChannelManager $rssChannelManager,
        private UserManager $userManager,
        private EntryFlagManager $entryFlagManager,
    ){ }

    #[Route('/rss-entry/listing/{idUser}/{idChannel}/{page}', name: 'rssEntry_listing')]
    public function listing(int $idUser=0, int $idChannel=0, int $page=1):Response{
        /**@var User $loggedUser */
        $loggedUser = $this->getUser();
        $users = $this->userManager->userRepository->findBy([], ['surname'=>'ASC']);
        $idUser = empty($idUser) ? $users[0]->getId() : $idUser;
        $channels = $this->rssChannelManager->channelRepository->findBy(['idUser'=>$idUser], ['title'=>'ASC']);
        if(empty($idChannel)){
            $idChannel = isset($channels[0]) ? $channels[0]->getId() : 0;
        }

        $idsEntries = [];
        $rssEntries = $this->rssEntryManager->entryRepository->getForListing($idChannel, $page, Entry::ITEMS_PER_PAGE);
        foreach($rssEntries as $rssEntry){
            $idsEntries[] = $rssEntry->getId();
        }
        $countRssEntries = $this->rssEntryManager->entryRepository->countEntries($idChannel);
        $lastPage = ceil($countRssEntries / Entry::ITEMS_PER_PAGE);

        $allFlags = $this->entryFlagManager->getAllIdToFlag($loggedUser->getId());
        $statuses = $this->rssEntryManager->getIdToEntryUserStatus($loggedUser->getId(), $idsEntries);

        return $this->render('webTemplates/rssEntry/listing.html.twig', [
            'rssEntries' => $rssEntries,
            'countRssEntries' => $countRssEntries,
            'lastPageNumber' => $lastPage,
            'page' => $page,
            'users' => $users,
            'channels' => $channels,
            'idUser' => $idUser,
            'idChannel' => $idChannel,
            'statuses' => $statuses,
            'allFlags' => $allFlags
        ]);
    }

    #[Route('/rss-entry/edit/{id}', name: 'rssEntry_edit')]
    public function edit(int $id, Request $request):Response{
        /**@var Entry $entry */
        $entry = $this->rssEntryManager->entryRepository->find($id);
        if($entry === null){
            throw new NotFoundHttpException('zaznam nenájdeny');
        }
        return $this->save($request, $entry);
    }

    #[Route('/rss-entry/new/{idChannel}', name: 'rssEntry_new')]
    public function new(int $idChannel, Request $request):Response{
        $entry = new Entry();
        $entry->setIdRssChannel($idChannel);
        return $this->save($request, $entry);
    }

    private function save(Request $request, Entry $entry):Response{
        /**@var User $loggedUser */
        $loggedUser = $this->getUser();
        $typeData = new RssEntryTypeData();
        $typeData->setTitle($entry->getTitle());
        $typeData->setIdRssChannel($entry->getIdRssChannel());
        $typeData->setLink($entry->getLink());
        $typeData->setSummary($entry->getSummary());
        $typeData->setContent($entry->getContent());

        $idRssChannels = $this->rssChannelManager->getChannelsForForm($loggedUser);
        $form = $this->createForm(RssEntryType::class, $typeData, ['idRssChannels'=>array_flip($idRssChannels)]);
        $form->handleRequest($request);
        $isEdit = !empty($entry->getId());

        $formSubmitUrl = $this->generateUrl('rssEntry_new', ['idChannel'=>$entry->getIdRssChannel()]);
        if($isEdit){
            $formSubmitUrl = $this->generateUrl('rssEntry_edit', ['id' => $entry->getId()]);
        }

        $systemErrors = [];
        if($form->isSubmitted() && $form->isValid()){
            try{
                $this->rssEntryManager->saveEntryFromType($entry, $typeData);
                $channel = $this->rssChannelManager->channelRepository->find($entry->getIdRssChannel());
                if($isEdit){
                    return $this->render('webTemplates/rssEntry/refreshDetail.html.twig', [
                        'id'=>$entry->getId()
                    ]);
                }else{
                    return $this->render('webTemplates/rssEntry/refreshListing.html.twig', [
                        'idUser'=>$channel->getIdUser(),
                        'idChannel'=>$entry->getIdRssChannel()
                    ]);
                }
            }catch(Exception $e){
                $systemErrors[] = $e->getMessage();
            }
        }
        // formular
        return $this->render('webTemplates/rssEntry/form.html.twig', [
            'form' => $form->createView(),
            'formSubmitUrl' => $formSubmitUrl,
            'systemErrors' => $systemErrors
        ]);
    }

    #[Route('/rss-entry/delete/{id}', name: 'rssEntry_delete')]
    public function delete(int $id): Response {
        /**@var User $loggedUser */
        $loggedUser = $this->getUser();
        /**@var Entry $entry */
        $entry = $this->rssEntryManager->entryRepository->find($id);
        $channel = $this->rssChannelManager->channelRepository->find($entry->getIdRssChannel());

        try{
            $this->rssEntryManager->delete($entry, $loggedUser);
        }catch(Exception $e){
            throw new NotFoundHttpException($e->getMessage());
        }
        return $this->render('webTemplates/rssEntry/refreshListing.html.twig', [
            'idUser'=>$channel->getIdUser(),
            'idChannel'=>$entry->getIdRssChannel()
        ]);
    }

    #[Route('/rss-entry/detail/{id}', name: 'rssEntry_detail')]
    public function detail(int $id): Response {
        return $this->getDetailResponse($id);
    }

    #[Route('/rss-entry/toggle-read/{id}', name: 'rssEntry_toggleRead')]
    public function toggleRead(int $id): Response {
        /**@var User $loggedUser */
        $loggedUser = $this->getUser();

        try{
            $this->rssEntryManager->toggleIsReadEntry($id, $loggedUser->getId());
        }catch(Exception $e){
            throw new NotFoundHttpException($e->getMessage());
        }
        return $this->getDetailResponse($id);
    }

    #[Route('/rss-entry/set-flag/{id}/{idFlag}', name: 'rssEntry_setFlag')]
    public function setFlag(int $id, int $idFlag): Response {
        /**@var User $loggedUser */
        $loggedUser = $this->getUser();

        try{
            $this->rssEntryManager->setEntryFlag($id, $loggedUser->getId(), $idFlag);
        }catch(Exception $e){
            throw new NotFoundHttpException($e->getMessage());
        }

        return $this->getDetailResponse($id);
    }

    private function getDetailResponse(int $id):Response{
        /**@var User $loggedUser */
        $loggedUser = $this->getUser();
        $entry = $this->rssEntryManager->entryRepository->find($id);
        $channel = $this->rssChannelManager->channelRepository->find($entry->getIdRssChannel());
        $flags = $this->entryFlagManager->entryFlagRepository->findBy(['idUser'=>$loggedUser->getId()], ['name'=>'ASC']);
        /**@var EntryUserStatus $entryStatus */
        $entryStatus = $this->rssEntryManager->entryUserStatusRepository->findOneBy([
            'idRssEntry'=>$entry->getId(),
            'idUser'=>$loggedUser->getId()
        ]);
        $user = $this->userManager->userRepository->find($channel->getIdUser());
        $flag = null;
        if($entryStatus !== null && !empty($entryStatus->getIdRssEntryFlag())){
            $flag = $this->entryFlagManager->entryFlagRepository->find($entryStatus->getIdRssEntryFlag());
        }

        return $this->render('webTemplates/rssEntry/detail.html.twig', [
            'entry' => $entry,
            'channel' => $channel,
            'entryStatus' => $entryStatus,
            'flags' => $flags,
            'user' => $user,
            'flag' => $flag
        ]);
    }
}
