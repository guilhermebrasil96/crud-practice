<?php

declare(strict_types=1);

namespace App\Moto\Application;

use App\Moto\Domain\Moto;
use App\Moto\Domain\MotoRepository;

final class UpdateMoto
{
    public function __construct(
        private readonly MotoRepository $motoRepository
    ) {}
    

    public function __invoke(int $id, array $data): ?Moto
    {
        $moto = $this->motoRepository->findById($id);
        if ($moto === null) {
            return null;
        }
    
        if (array_key_exists('name', $data)) {
            $moto->setName($data['name']);
        }
        if (array_key_exists('description', $data)) {
            $moto->setDescription($data['description']);
        }
        if (array_key_exists('price', $data)) {
            $moto->setPrice($data['price']);
        }
        $this->motoRepository->save($moto);
        return $moto;
    }
}