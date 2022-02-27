<?php
namespace App\Entity\rss;

use DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity(repositoryClass: EntryRepository::class)]
#[Table(name: "rss_entry")]
class Entry
{
    const ITEMS_PER_PAGE = 10;

    #[Id, Column(name: 'id', type: "integer", options: ["unsigned" => true]), GeneratedValue(strategy: "AUTO")]
    private ?int $id = null;

    #[Column(name: "id_rss_channel", type: "integer", nullable: false)]
    private ?int $idRssChannel = null;

    #[Column(name: "title", type: "string", length: 255, nullable: false)]
    private ?string $title = null;

    #[Column(name: "link", type: "string", length: 255, nullable: false)]
    private ?string $link = null;

    #[Column(name: "published_at", type: "datetime", nullable: true)]
    private ?DateTime $publishedAt = null;

    #[Column(name: "updated_at", type: "datetime", nullable: true)]
    private ?DateTime $updatedAt = null;

    #[Column(name: "summary", type: "text", nullable: true)]
    private ?string $summary = null;

    #[Column(name: "content", type: "text", nullable: true)]
    private ?string $content = null;

    //-------------- GET / SET --------------
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

    public function getPublishedAt():?DateTime{
        return $this->publishedAt;
    }

    public function setPublishedAt(?DateTime $publishedAt):void{
        $this->publishedAt = $publishedAt;
    }

    public function getUpdatedAt():?DateTime{
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTime $updatedAt):void{
        $this->updatedAt = $updatedAt;
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


}
