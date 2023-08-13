<?php

namespace App\Repository;

use App\Entity\Script;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Script>
 *
 * @method Script|null find($id, $lockMode = null, $lockVersion = null)
 * @method Script|null findOneBy(array $criteria, array $orderBy = null)
 * @method Script[]    findAll()
 * @method Script[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScriptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Script::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Script $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Script $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findOneById($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.id = :id')
            ->setParameter('id', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllScript()
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findLikeNameOrDesc($keyword)
    {
        $queryBuilder = $this->createQueryBuilder('s');
        return $queryBuilder
            ->where($queryBuilder->expr()->like('s.name', ':keyword'))
            ->orWhere($queryBuilder->expr()->like('s.description', ':keyword'))
            ->setParameter('keyword', '%' . $keyword . '%')
            ->orderBy('s.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }
    // /**
    //  * @return Script[] Returns an array of Script objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Script
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
