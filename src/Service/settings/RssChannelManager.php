<?php
namespace App\Service\settings;

use App\Entity\rss\Channel;
use App\Entity\rss\ChannelRepository;
use App\Entity\user\User;
use App\Form\rss\settings\RssChannelTypeData;
use App\Service\AbstractBaseManager;
use Doctrine\ORM\EntityManagerInterface;
use Sentia\Utils\SentiaUtils;

class RssChannelManager extends AbstractBaseManager{

    public ChannelRepository $channelRepository;

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected SentiaUtils $utils
    ){
        parent::__construct($entityManager, $utils);
        $this->channelRepository = $this->em->getRepository(Channel::class);
    }

    public function saveRssChannelFromType(Channel $channel, RssChannelTypeData $saveData): void {
        $channel->setTitle($saveData->getTitle());
        $channel->setSubtitle($saveData->getSubtitle());
        $this->saveSimple($channel);
    }

    public function getChannelsForForm(User $user): array {
        $ret = [];
        $channels = $this->channelRepository->findBy(['idUser' => $user->getId()], ['title' => 'ASC']);
        foreach($channels as $channel){
            $ret[$channel->getId()] = $channel->getTitle();
        }
        return $ret;
    }

}
