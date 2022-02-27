<?php
namespace App\Form\rss\settings;

use Symfony\Component\Validator\Constraints as Assert;

class RssChannelTypeData {
    private ?int $id = null;
    #[Assert\NotBlank(message: 'Názov nebol zadaný')]
    private ?string $title = null;
    private ?string $subtitle = null;

    //-------------- getters / setters -----------------
    public function getId():?int{
        return $this->id;
    }

    public function setId(?int $id):void{
        $this->id = $id;
    }

    public function getTitle():?string{
        return $this->title;
    }

    public function setTitle(?string $title):void{
        $this->title = $title;
    }

    public function getSubtitle():?string{
        return $this->subtitle;
    }

    public function setSubtitle(?string $subtitle):void{
        $this->subtitle = $subtitle;
    }




    //----------------- form transformations ------------------
//    public function copyDataToEntity(Event $item){
//        $item->setId($this->getId());
//        $item->setType($this->getType());
//        $item->setTitle($this->getTitle());
//        $item->setDescription($this->getDescription());
//        $item->setDateFrom($this->getDateFrom());
//        $item->setDateTill($this->getDateTill());
//        $item->setColor($item->getTypeBgColor());
//    }
//
//    public function copyDataFromEntity(Event $item){
//        $this->setId($item->getId());
//        $this->setType($item->getType());
//        $this->setTitle($item->getTitle());
//        $this->setDescription($item->getDescription());
//        $this->setDateFrom($item->getDateFrom());
//        $this->setDateTill($item->getDateTill());
//    }
}
