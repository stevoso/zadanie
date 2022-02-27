<?php
namespace App\Entity\rss;

use DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity(repositoryClass: EntryUserStatusRepository::class)]
#[Table(name: "rss_entry_user_status")]
class EntryUserStatus
{
    #[Id, Column(name: 'id', type: "integer", options: ["unsigned" => true]), GeneratedValue(strategy: "AUTO")]
    private ?int $id = null;

    #[Column(name: "id_rss_entry", type: "integer", nullable: false)]
    private ?int $idRssEntry = null;

    #[Column(name: "id_rss_entry_flag", type: "integer", nullable: true)]
    private ?int $idRssEntryFlag = null;

    #[Column(name: "id_user", type: "integer", nullable: false)]
    private ?int $idUser = null;

    #[Column(name: "read_at", type: "datetime", nullable: true)]
    private ?DateTime $readAt = null;

    //-------------- GET / SET --------------
    public function getId():?int{
        return $this->id;
    }

    public function setId(?int $id):void{
        $this->id = $id;
    }

    public function getIdRssEntry():?int{
        return $this->idRssEntry;
    }

    public function setIdRssEntry(?int $idRssEntry):void{
        $this->idRssEntry = $idRssEntry;
    }

    public function getIdRssEntryFlag():?int{
        return $this->idRssEntryFlag;
    }

    public function setIdRssEntryFlag(?int $idRssEntryFlag):void{
        $this->idRssEntryFlag = $idRssEntryFlag;
    }

    public function getIdUser():?int{
        return $this->idUser;
    }

    public function setIdUser(?int $idUser):void{
        $this->idUser = $idUser;
    }

    public function getReadAt():?DateTime{
        return $this->readAt;
    }

    public function setReadAt(?DateTime $readAt):void{
        $this->readAt = $readAt;
    }

    //--- OTHER ---
    public function isRead(): bool {
        return $this->readAt !== null;
    }
}
