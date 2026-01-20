<?php

declare(strict_types=1);

namespace App\Moto\Application;

use App\Moto\Domain\Moto;
use App\Moto\Domain\MotoRepository;

final class GetMoto
{
    public function __construct(
        private readonly MotoRepository $motoRepository
    ) {
    }

    public function __invoke(int $id): ?Moto
    {
        return $this->motoRepository->findById($id);
    }
}