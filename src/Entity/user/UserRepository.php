<?php
namespace App\Entity\user;

use App\Entity\subject\Subject;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Exception;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Uid\Uuid;

class UserRepository extends EntityRepository implements UserProviderInterface, UserLoaderInterface{

    /**
     * vracia pole objektov User
     * @return User[]
     */
    public function getAll(): array {
        $query = $this->getEntityManager()->createQuery('SELECT u FROM ' . User::class . ' u ORDER BY u.surname');
        return $query->getResult();
    }

    /**
     * vracia objekty User podla idciek
     * @return User[]
     */
    public function getByIds(array $ids): array {
        return $this->findBy(['id' => $ids]);
    }

    //------------------------------------------
    //----- metody potrebne na autorizaciu -----
    //------------------------------------------
    /**
     * tuto metodu vyzaduje UserLoaderInterface a UserProviderInterface na autentifikaciu
     * @param string $username
     * @return mixed|null|UserInterface
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadUserByUsername($username){
        $query = $this->getEntityManager()->createQuery('SELECT u FROM ' . User::class . ' u WHERE u.login=:login');
        return $query->setParameter('login', $username)->getOneOrNullResult();
    }

    public function loadUserByIdentifier(string $identifier):UserInterface{
        $query = $this->getEntityManager()->createQuery('SELECT u FROM ' . User::class . ' u WHERE u.login=:login');
        return $query->setParameter('login', $identifier)->getOneOrNullResult();
    }

    /**
     * vyzaduje UserProviderInterface
     * @param UserInterface $user
     * @return mixed|null|UserInterface
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function refreshUser(UserInterface $user){
        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    /**
     * vyzaduje UserProviderInterface
     * @param string $class
     */
    public function supportsClass($class): bool {
        return $class === User::class;
    }

    //------------------------------
    //--------- FOR API ------------
    //------------------------------
    /**
     * vracia pouzivatelov podla filtra
     */
    public function findByUserFilter(\App\Model\subject\UserFilter $filter): array {
        $query = $this->prepareQuery2($filter, false);
        // strankovanie
        if($filter->numPerPage > 0){
            $first = $filter->pageNumber*$filter->numPerPage;
            $query->setFirstResult($first)->setMaxResults($filter->numPerPage);
        }
        return $query->getResult();
    }

    /**
     * vracia cislo - pocet vsetkych pouzivatelov, ktori vyhovuju filtru
     */
    public function countByUserFilter(\App\Model\subject\UserFilter $filter): int {
        $query = $this->prepareQuery2($filter, true);
        try{
            return $query->getSingleScalarResult();
        }catch(Exception $exception){
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * vracia uuid-cka podla filtra
     */
    public function getUuidStringsByUserFilter(\App\Model\subject\UserFilter $filter): array {
        $query = $this->prepareQuery2($filter, false);
        /**@var User[] $users */
        $users = $query->getResult();
        $ret = [];
        foreach($users as $user){
            $ret[] = $user->getSubject()->getUuid()->toRfc4122();
        }
        return $ret;
    }

    /**
     * priprava DQL query pre ine metody
     */
    private function prepareQuery2(\App\Model\subject\UserFilter $filter, bool $isCount):Query{
        $selectAddon = $isCount ? 'COUNT(u.id)' : 'u';

        $brokerAddon = 's.idBroker IS NULL';
        if($filter->idBroker !== null){
            $brokerAddon = 's.idBroker='.$filter->idBroker;
        }
        $idUsersStrictAddon = empty($filter->idSubjectUsersRange) ? '' : ' AND s.id IN (:idSubjectUsers)';

        $query = $this->getEntityManager()->createQuery('SELECT '.$selectAddon.' FROM '.User::class.' u
        JOIN u.subject s WHERE s.type='.Subject::TYPE_USER.' AND '.$brokerAddon.'
        AND (1=0
            ' . ($filter->showActive ? ' OR u.isActive=1' : '') . '
            ' . ($filter->showInactive ? ' OR u.isActive=0' : '') . '
        )
        AND (1=0
            ' . ($filter->showBrokerEmployees ? ' OR u.isBrokerEmployee=1' : '') . '
            ' . ($filter->showNotBrokerEmployees ? ' OR u.isBrokerEmployee=0' : '') . '
        )
        AND (1=0
            ' . ($filter->showNotDeleted ? ' OR u.isDeleted=0' : '') . '
            ' . ($filter->showDeleted ? ' OR u.isDeleted=1' : '') . '
        )'.$idUsersStrictAddon.'
        ORDER BY u.surname');

        if(!empty($filter->idSubjectUsersRange)){
            $query->setParameter('idSubjectUsers', $filter->idSubjectUsersRange);
        }

        return $query;
    }

    /**
     * vracia pouzivatelov pre rychle vyhladavanie... vyhladava retazec v priezvisku, emailoch a pri logine
     * @return User[]
     */
    public function getForSearch(string $term, ?int $idBroker):array{
        $brokerAddon = $idBroker === null ? 's.idBroker IS NULL' : 's.idBroker=:idBroker';
        $query = $this->getEntityManager()->createQuery('SELECT u FROM ' . User::class . ' u
        LEFT JOIN u.subject s WHERE '.$brokerAddon.' AND (LOWER(u.surname) LIKE LOWER(:term)
        OR LOWER(u.login) LIKE LOWER(:term) OR LOWER(u.email) LIKE LOWER(:term)) ORDER BY u.surname');
        $query->setParameter('term', '%' . $term . '%');
        if($idBroker !== null){
            $query->setParameter('idBroker', $idBroker);
        }
        return $query->getResult();
    }
}
