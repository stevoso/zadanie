<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sentia\Utils\SentiaUtils;

abstract class AbstractBaseManager{

    public function __construct(
        protected EntityManagerInterface $em,
        protected SentiaUtils $utils
    ){ }

    /**
     * remove multiple objects
     */
    public function deleteItems(array $items = []):void{
        if(count($items) > 0){
            foreach($items as $item){
                $this->em->remove($item);
            }
            $this->em->flush();
        }
    }

    /**
     * save Entity object
     * $item - Entity object
     */
    public function saveSimple($item):bool{
        $this->em->persist($item);
        $this->em->flush();
        return true;
    }

    /**
     * save Entity object
     * $item - Entity object
     */
    public function saveSimpleEntities(array $items):void{
        if(count($items) > 0){
            foreach($items as $item){
                $this->em->persist($item);
            }
            $this->em->flush();
        }
    }

}
