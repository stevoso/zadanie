<?php
namespace App\Service\settings;

use App\Entity\rss\EntryFlag;
use App\Entity\rss\EntryFlagRepository;
use App\Form\rss\settings\RssEntryFlagTypeData;
use App\Service\AbstractBaseManager;
use Doctrine\ORM\EntityManagerInterface;
use Sentia\Utils\SentiaUtils;

class EntryFlagManager extends AbstractBaseManager{
    public EntryFlagRepository $entryFlagRepository;

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected SentiaUtils $utils
    ){
        parent::__construct($entityManager, $utils);
        $this->entryFlagRepository = $this->em->getRepository(EntryFlag::class);
    }

    public function saveEntryFlagFromType(EntryFlag $entryFlag, RssEntryFlagTypeData $saveData): void {
        $entryFlag->setName($saveData->getName());
        $entryFlag->setColor($saveData->getColor());
        $this->saveSimple($entryFlag);
    }

    public function getAllIdToFlag(int $idUser):array{
        $flags = $this->entryFlagRepository->findBy(['idUser'=>$idUser]);
        $ret = [];
        foreach($flags as $flag){
            $ret[$flag->getId()] = $flag;
        }
        return $ret;
    }
}
