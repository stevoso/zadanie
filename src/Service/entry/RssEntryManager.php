<?php
namespace App\Service\entry;

use App\Entity\rss\Channel;
use App\Entity\rss\ChannelRepository;
use App\Entity\rss\Entry;
use App\Entity\rss\EntryRepository;
use App\Entity\rss\EntryUserStatus;
use App\Entity\rss\EntryUserStatusRepository;
use App\Entity\user\User;
use App\Form\rss\RssEntryTypeData;
use App\Service\AbstractBaseManager;
use App\Service\ParametersApp;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sentia\Utils\SentiaUtils;

class RssEntryManager extends AbstractBaseManager{
    public EntryRepository $entryRepository;
    private ChannelRepository $channelRepository;
    public EntryUserStatusRepository $entryUserStatusRepository;

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected SentiaUtils $utils,
        private ParametersApp $parametersApp,
    ){
        parent::__construct($entityManager, $utils);
        $this->entryRepository = $this->em->getRepository(Entry::class);
        $this->channelRepository = $this->em->getRepository(Channel::class);
        $this->entryUserStatusRepository = $this->em->getRepository(EntryUserStatus::class);
    }

    public function saveEntryFromType(Entry $entry, RssEntryTypeData $saveData): void {
        if(empty($entry->getId())){
            $entry->setPublishedAt(new DateTime());
        }else{
            $entry->setUpdatedAt(new DateTime());
        }
        $entry->setTitle($saveData->getTitle());
        $entry->setIdRssChannel($saveData->getIdRssChannel());
        $entry->setLink($saveData->getLink());
        $entry->setContent($saveData->getContent());
        $entry->setSummary($saveData->getSummary());
        $this->saveSimple($entry);
    }

    public function delete(?Entry $entry, User $user): void {
        if($entry === null){
            throw new Exception('zaznam nenájdeny');
        }

        $channel = $this->channelRepository->find($entry->getIdRssChannel());
        if($channel->getIdUser() !== $user->getId()){
            throw new Exception('nemôžte vymazať cudzí záznam');
        }

        $this->deleteItems([$entry]);
    }

    public function toggleIsReadEntry(int $idEntry, int $idUser): void {
        $entryUserStatus = $this->entryUserStatusRepository->findOneBy(['idUser'=>$idUser, 'idRssEntry'=>$idEntry]);
        if($entryUserStatus === null){
            $entryUserStatus = new EntryUserStatus();
            $entryUserStatus->setIdUser($idUser);
            $entryUserStatus->setIdRssEntry($idEntry);
        }

        if($entryUserStatus->getReadAt() === null){
            $entryUserStatus->setReadAt(new DateTime());
        }else{
            $entryUserStatus->setReadAt(null);
        }
        $this->saveSimple($entryUserStatus);
    }

    public function setEntryFlag(int $idEntry, int $idUser, int $idEntryFlag): void {
        $entryUserStatus = $this->entryUserStatusRepository->findOneBy(['idUser'=>$idUser, 'idRssEntry'=>$idEntry]);
        if($entryUserStatus === null){
            $entryUserStatus = new EntryUserStatus();
            $entryUserStatus->setIdUser($idUser);
            $entryUserStatus->setIdRssEntry($idEntry);
        }
        $entryUserStatus->setIdRssEntryFlag(empty($idEntryFlag) ? null : $idEntryFlag);
        $this->saveSimple($entryUserStatus);
    }

    public function getIdToEntryUserStatus(int $idUser, array $idEntries):array{
        $entryUserStatuses = $this->entryUserStatusRepository->findBy(['idUser'=>$idUser, 'idRssEntry'=>$idEntries]);
        $ret = [];
        foreach($entryUserStatuses as $entryUserStatus){
            $ret[$entryUserStatus->getIdRssEntry()] = $entryUserStatus;
        }
        return $ret;
    }

    public function exportToXml():void {
        $allEntries = $this->entryRepository->getForExport();
        $fileContent = '<feed>
<title>Nebbia zadanie</title>
<subtitle>Všetky príspvky</subtitle>
<link href="https://zadanie.prestiz.sk/rss.xml" type="application/atom+rss" rel="alternate"/>
<id>1</id>
<updated>'.date('Y-m-dTH:i:s').'+02:00</updated>';
        foreach($allEntries as $entry){
            $updateDate = $entry->getUpdatedAt() === null ? $entry->getPublishedAt() : $entry->getUpdatedAt();
            $fileContent .= '<entry>
  <id>'.$entry->getId().'</id>
  <title type="text">'.$entry->getTitle().'</title>
  <published>'.$entry->getPublishedAt()->format('Y-m-dTH:i:s').'+02:00</published>
    <author>
      <name>Zadanie</name>
    </author>
  <link rel="alternate" href="'.$entry->getLink().'"/>
<summary type="text">'.$entry->getSummary().'</summary>
<content type="text">'.$entry->getContent().'</content>
<category term="Aktuality"/>
<updated>'.$updateDate->format('Y-m-dTH:i:s').'+02:00</updated>
</entry>';
        }
        $fileContent .= '</feed>';
        file_put_contents($this->parametersApp->pathRoot.'/public/rss.xml', $fileContent);
    }
}
