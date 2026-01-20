<?php

declare(strict_types=1);

namespace App\Car\Infrastructure\Persistence;

use App\Car\Domain\Car;
use App\Car\Domain\CarRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Car>
 */
final class DoctrineCarRepository extends ServiceEntityRepository implements CarRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Car::class);
    }

    /** @return Car[] */
    public function findAll(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findById(int $id): ?Car
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function save(Car $car): void
    {
        $this->getEntityManager()->persist($car);
        $this->getEntityManager()->flush();
    }

    public function remove(Car $car): void
    {
        $this->getEntityManager()->remove($car);
        $this->getEntityManager()->flush();
    }
}