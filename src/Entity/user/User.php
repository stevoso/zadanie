<?php
namespace App\Entity\user;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Symfony\Component\Security\Core\User\UserInterface;

#[Entity(repositoryClass: UserRepository::class)]
#[Table(name: "user")]
class User implements UserInterface
{
    #[Id, Column(name: 'id', type: "integer", options: ["unsigned" => true]), GeneratedValue(strategy: "AUTO")]
    private ?int $id = null;

    #[Column(name: "name", type: "string", length: 20, nullable: true)]
    private ?string $name = null;

    #[Column(name: "surname", type: "string", length: 60, nullable: false)]
    private ?string $surname = null;

    #[Column(name: "login", type: "string", length: 20, nullable: false)]
    private ?string $login = null;

    // prihlasovacie heslo (hash)
    #[Column(name: "password", type: "string", length: 255, nullable: false)]
    private ?string $password = null;

    //-------------- GET / SET --------------
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

    public function getSurname():?string{
        return $this->surname;
    }

    public function setSurname(?string $surname):void{
        $this->surname = $surname;
    }

    public function getLogin():?string{
        return $this->login;
    }

    public function setLogin(?string $login):void{
        $this->login = $login;
    }

    public function getPassword():?string{
        return $this->password;
    }

    public function setPassword(?string $password):void{
        $this->password = $password;
    }

    //-------------- INE -----------------
    public function getFullName():string{
        return trim($this->surname.' '.$this->name);
    }

    public function getUserIdentifier():string{
        return $this->login;
    }

    public function getUsername():string{
        return $this->login;
    }

    public function getSalt():?string{
        return null;
    }

    public function eraseCredentials(){
    }

    public function __call($name, $arguments){
    }

    public function getRoles():array{
        return [];
    }
}
