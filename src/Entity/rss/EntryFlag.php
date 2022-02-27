<?php
namespace App\Entity\rss;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity(repositoryClass: EntryFlagRepository::class)]
#[Table(name: "rss_entry_flag")]
class EntryFlag
{
    public static array $colors = [
        'fialová' => '#85085f',
        'červená' => '#db1d2c',
        'modrá' => '#138deb',
        'oranžová' => '#fc8803',
        'zelená' => '#09de42',
        'žltá' => '#cccf1d',
    ];

    #[Id, Column(name: 'id', type: "integer", options: ["unsigned" => true]), GeneratedValue(strategy: "AUTO")]
    private ?int $id = null;

    #[Column(name: "id_user", type: "integer", nullable: false)]
    private ?int $idUser = null;

    #[Column(name: "name", type: "string", length: 20, nullable: false)]
    private ?string $name = null;

    #[Column(name: "color", type: "string", length: 20, nullable: false)]
    private ?string $color = null;

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

}
