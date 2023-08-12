<?php

namespace App\Repository;

use App\Entity\NewmanTask;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NewmanTask>
 *
 * @method NewmanTask|null find($id, $lockMode = null, $lockVersion = null)
 * @method NewmanTask|null findOneBy(array $criteria, array $orderBy = null)
 * @method NewmanTask[]    findAll()
 * @method NewmanTask[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewmanTaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NewmanTask::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(NewmanTask $entity, bool $flush = true): void
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
    public function remove(NewmanTask $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }


    public function findUnfinishedTasks()
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.active = :value')
            ->setParameter('value', 0)
            ->orderBy('n.created_at', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllTasks()
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.active = :value')
            ->setParameter('value', 1)
            ->orderBy('n.created_at', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return NewmanTask[] Returns an array of NewmanTask objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NewmanTask
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
