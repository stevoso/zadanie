<?php
namespace App\Service\user;

use App\Entity\user\User;
use App\Entity\user\UserRepository;
use App\Form\user\registration\RegistrationTypeData;
use App\Service\AbstractBaseManager;
use Doctrine\ORM\EntityManagerInterface;
use Sentia\Utils\SentiaUtils;

class UserManager extends AbstractBaseManager {

    public UserRepository $userRepository;

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected SentiaUtils $utils,
    ){
        parent::__construct($entityManager, $utils);
        $this->userRepository = $this->em->getRepository(User::class);
    }

    public function createUser(RegistrationTypeData $registrationTypeData): void {
        $tmpUser = $this->userRepository->findOneBy(['login' => $registrationTypeData->getLogin()]);
        if($tmpUser !== null){
            throw new \Exception('Používateľ s prihlasovacím menom \''.$registrationTypeData->getLogin().'\' už existuje');
        }

        $user = new User();
        $user->setName($registrationTypeData->getName());
        $user->setSurname($registrationTypeData->getSurname());
        $user->setLogin($registrationTypeData->getLogin());
        $user->setPassword(password_hash($registrationTypeData->getPassword(), PASSWORD_BCRYPT));
        $this->saveSimple($user);
    }

}
