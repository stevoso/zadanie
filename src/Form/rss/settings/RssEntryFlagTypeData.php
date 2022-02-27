<?php
namespace App\Form\rss\settings;

use Symfony\Component\Validator\Constraints as Assert;

class RssEntryFlagTypeData {
    private ?int $id = null;
    #[Assert\NotBlank(message: 'Názov nebol zadaný')]
    private ?string $name = null;
    #[Assert\NotBlank(message: 'Farba nebola zvolená')]
    private ?string $color = null;

    //-------------- getters / setters -----------------
    public function getId():?int{
        return $this->id;
    }

    public function setId(?int $id):void{
        $this->id = $id;
    }

    public function getName():?string{
        return $this->name;
    }

    public function setName(?string $name):void{
        $this->name = $name;
    }

    public function getColor():?string{
        return $this->color;
    }

    public function setColor(?string $color):void{
        $this->color = $color;
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
