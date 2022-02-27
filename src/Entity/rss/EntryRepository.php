<?php
namespace App\Entity\rss;

use Doctrine\ORM\EntityRepository;

class EntryRepository extends EntityRepository {

    /**
     * @return Entry[]
     */
    public function getForListing(int $idChannel, $page, $itemsPerPage):array{
        $query = $this->getEntityManager()->createQuery('SELECT e FROM '.Entry::class.' e
        WHERE e.idRssChannel=:idChannel ORDER BY e.publishedAt DESC');
        $query->setParameter('idChannel', $idChannel);
        if($page > 0){
            $first = ($page-1)*$itemsPerPage;
            $query->setFirstResult($first)->setMaxResults($itemsPerPage);
        }
        return $query->getResult();
    }

    public function countEntries(int $idChannel): int {
        $query = $this->getEntityManager()->createQuery('SELECT COUNT(e.id) FROM '.Entry::class.' e
        WHERE e.idRssChannel=:idChannel ORDER BY e.publishedAt DESC');
        $query->setParameter('idChannel', $idChannel);
        return $query->getSingleScalarResult();
    }

    /**
     * @return Entry[]
     */
    public function getForExport():array{
        $query = $this->getEntityManager()->createQuery('SELECT e FROM '.Entry::class.' e
        GROUP BY e.link ORDER BY e.publishedAt DESC');
        return $query->getResult();
    }

}
