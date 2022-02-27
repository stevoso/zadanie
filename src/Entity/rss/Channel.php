<?php
namespace App\Entity\rss;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity(repositoryClass: ChannelRepository::class)]
#[Table(name: "rss_channel")]
class Channel
{
    #[Id, Column(name: 'id', type: "integer", options: ["unsigned" => true]), GeneratedValue(strategy: "AUTO")]
    private ?int $id = null;

    #[Column(name: "id_user", type: "integer", nullable: false)]
    private ?int $idUser = null;

    #[Column(name: "title", type: "string", length: 255, nullable: false)]
    private ?string $title = null;

    #[Column(name: "subtitle", type: "string", length: 255, nullable: false)]
    private ?string $subtitle = null;

    //-------------- GET / SET --------------
    public function getId():?int{
        return $this->id;
    }

    public function setId(?int $id):void{
        $this->id = $id;
    }

    public function getIdUser():?int{
        return $this->idUser;
    }

    public function setIdUser(?int $idUser):void{
        $this->idUser = $idUser;
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


}
