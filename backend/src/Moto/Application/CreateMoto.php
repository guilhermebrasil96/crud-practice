<?php

declare(strict_types=1);

namespace App\Moto\Application;

use App\Moto\Domain\Moto;
use App\Moto\Domain\MotoRepository;

final class CreateMoto
{
    public function __construct(
        private readonly MotoRepository $motoRepository
    ) {
    }

    public function __invoke(array $data): Moto
    {
        $name = $data['name'] ?? '';
        if (trim($name) === '') {
            throw new \InvalidArgumentException('name is required');
        }

        $moto = new Moto();
        $moto->setName(trim($name));
        $moto->setDescription($data['description'] ?? '');
        if (array_key_exists('price', $data)) {
            $moto->setPrice($data['price']);
        }

        $this->motoRepository->save($moto);

        return $moto;
    }
}