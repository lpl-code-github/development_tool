<?php

namespace App\Repository;

use App\Entity\DatabaseBackup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DatabaseBackup>
 *
 * @method DatabaseBackup|null find($id, $lockMode = null, $lockVersion = null)
 * @method DatabaseBackup|null findOneBy(array $criteria, array $orderBy = null)
 * @method DatabaseBackup[]    findAll()
 * @method DatabaseBackup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DatabaseBackupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DatabaseBackup::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(DatabaseBackup $entity, bool $flush = true): void
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
    public function remove(DatabaseBackup $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findAllOrderByCreatedAt(){
        return $this->createQueryBuilder('d')
            ->orderBy('d.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return DatabaseBackup[] Returns an array of DatabaseBackup objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DatabaseBackup
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
