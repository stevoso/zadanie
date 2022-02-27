<?php
namespace App\Form\user\registration;

use Symfony\Component\Validator\Constraints as Assert;

class RegistrationTypeData {
    private ?string $name = null;
    #[Assert\NotBlank(message: 'Zadajte priezvisko')]
    private ?string $surname = null;
    #[Assert\NotBlank(message: 'Zadajte login')]
    private ?string $login = null;
    #[Assert\NotBlank(message: 'Zadajte heslo')]
    private ?string $password = null;

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

}
