<?php

namespace App\Repository;

use App\Entity\ScriptTag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ScriptTag>
 *
 * @method ScriptTag|null find($id, $lockMode = null, $lockVersion = null)
 * @method ScriptTag|null findOneBy(array $criteria, array $orderBy = null)
 * @method ScriptTag[]    findAll()
 * @method ScriptTag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScriptTagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ScriptTag::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(ScriptTag $entity, bool $flush = true): void
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
    public function remove(ScriptTag $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findByScriptId($scriptId){
        return $this->createQueryBuilder('st')
            ->andWhere('st.script = :scriptId')
            ->setParameter('scriptId', $scriptId)
            ->getQuery()
            ->getResult();
    }

    public function findByScriptIdAndTagId($scriptId,$tagId){
        return $this->createQueryBuilder('st')
            ->andWhere('st.script = :scriptId')
            ->andWhere('st.tag = :tagId')
            ->setParameter('scriptId', $scriptId)
            ->setParameter('tagId', $tagId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // /**
    //  * @return ScriptTag[] Returns an array of ScriptTag objects
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
    public function findOneBySomeField($value): ?ScriptTag
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
