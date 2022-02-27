<?php
namespace App\Form\rss;

use Symfony\Component\Validator\Constraints as Assert;

class RssEntryTypeData {
    private ?int $id = null;
    #[Assert\NotBlank(message: 'Kanál nebol zvolený')]
    private ?int $idRssChannel = null;
    #[Assert\NotBlank(message: 'Nadpis nebol zadaný')]
    private ?string $title = null;
    #[Assert\NotBlank(message: 'Link nebol zadaný')]
    private ?string $link = null;
    private ?string $summary = null;
    private ?string $content = null;

    //-------------- getters / setters -----------------
    public function getId():?int{
        return $this->id;
    }

    public function setId(?int $id):void{
        $this->id = $id;
    }

    public function getIdRssChannel():?int{
        return $this->idRssChannel;
    }

    public function setIdRssChannel(?int $idRssChannel):void{
        $this->idRssChannel = $idRssChannel;
    }

    public function getTitle():?string{
        return $this->title;
    }

    public function setTitle(?string $title):void{
        $this->title = $title;
    }

    public function getLink():?string{
        return $this->link;
    }

    public function setLink(?string $link):void{
        $this->link = $link;
    }

    public function getSummary():?string{
        return $this->summary;
    }

    public function setSummary(?string $summary):void{
        $this->summary = $summary;
    }

    public function getContent():?string{
        return $this->content;
    }

    public function setContent(?string $content):void{
        $this->content = $content;
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
