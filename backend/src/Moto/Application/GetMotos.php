<?php

declare(strict_types=1);

namespace App\Moto\Application;

use App\Moto\Domain\MotoRepository;

final class GetMotos
{
    public function __construct(
        private readonly MotoRepository $motoRepository
    ) {
    }

    public function __invoke(): array
    {
        return $this->motoRepository->findAll();
    }
}