<?php

declare(strict_types=1);

namespace App\Moto\Infrastructure\Persistence;

use App\Moto\Domain\Moto;
use App\Moto\Domain\MotoRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Moto>
 */
class DoctrineMotoRepository extends ServiceEntityRepository implements MotoRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Moto::class);
    }

    /** @return Moto[] */
    public function findAll(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findById(int $id): ?Moto
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function save(Moto $moto): void
    {
        $this->getEntityManager()->persist($moto);
        $this->getEntityManager()->flush();
    }

    public function remove(Moto $moto): void
    {
        $this->getEntityManager()->remove($moto);
        $this->getEntityManager()->flush();
    }
    
}