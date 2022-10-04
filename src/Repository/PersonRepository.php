<?php

namespace App\Repository;

use App\Entity\Person;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class PersonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $mr)
    {
        parent::__construct($mr, Person::class);
    }

    public function add(Person $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Person $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function filterByAgeInterval($ageMin, $ageMax)
    {
        $qb = $this->createQueryBuilder('p');

        $this->interval($ageMin, $ageMax, $qb);

        return $qb->getQuery()
            ->getResult();
    }

    public function statsOnAgeInterval($ageMin, $ageMax)
    {
        $qb = $this->createQueryBuilder('p')
            ->select('avg(p.age) as avgAge, count(p.id) as nbPersons');
        $this->interval($ageMin, $ageMax, $qb);
        return $qb->getQuery()
            ->getScalarResult();
    }

    private function interval($ageMin, $ageMax, QueryBuilder $qb)
    {
        $qb->andWhere('p.age >= :ageMin and p.age <= :ageMax')
            ->setParameter('ageMin', $ageMin)
            ->setParameter('ageMax', $ageMax)
            ->orderBy('p.id', 'ASC');
    }

    public function search($keyword)
    {
        $qb = $this->createQueryBuilder('p')
            ->orWhere('p.firstname LIKE :keyword OR p.lastname LIKE :keyword')
            ->setParameter('keyword', '%'.$keyword.'%');

        return $qb->getQuery()->getResult();
    }
}
