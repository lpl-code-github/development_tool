<?php

namespace App\Repository;

use App\Entity\OperationLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 *
 * @method OperationLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method OperationLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method OperationLog[]    findAll()
 * @method OperationLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OperationLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OperationLog::class);
    }


    public function findObjArrayByFiled(array $filed){
        $qb = $this->getEntityManager()->createQueryBuilder();
        $query = $qb->select('ol')
            ->from(OperationLog::class, 'ol');

        foreach ($filed as $key => $value) {
            switch ($key){
                case "created_at":
                    $query->andWhere($qb->expr()->eq('DATE(ol.created_at)', ':created_at'))
                        ->setParameter('created_at', $value)
                        ->orderBy('ol.created_at', 'ASC');
                    break;
                case "start":
                    $query->andWhere($qb->expr()->gte('DATE(ol.created_at)', ':start'))
                        ->setParameter('start', $value);
                    break;
                case "target":
                    $query->andWhere($qb->expr()->lte('DATE(ol.created_at)', ':target'))
                        ->setParameter('target', $value);
                    break;
                case "type":
                    $query->andWhere('ol.type = :type')
                        ->setParameter('type', $value);
                    break;
                case "name":
                    $query->andWhere('ol.name = :name')
                        ->setParameter('name', $value);
                    break;
                default:
                    break;
            }
        }
        return $query->getQuery()
            ->getResult();
    }

    // /**
    //  * @return OperationLog[] Returns an array of OperationLog objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OperationLog
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
