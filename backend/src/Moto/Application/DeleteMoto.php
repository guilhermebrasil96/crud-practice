<?php

declare(strict_types=1);

namespace App\Moto\Application;

use App\Moto\Domain\MotoRepository;

final class DeleteMoto
{
    public function __construct(
        private readonly MotoRepository $motoRepository
    ) {
    }

    public function __invoke(int $id): bool
    {
        $moto = $this->motoRepository->findById($id);
        if ($moto === null) {
            return false;
        }
        $this->motoRepository->remove($moto);
        return true;
    }
}