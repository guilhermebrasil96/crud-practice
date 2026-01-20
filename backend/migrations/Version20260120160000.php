<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260120160000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add image column to product, Car, Moto';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE product ADD image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE car ADD image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE moto ADD image VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE product DROP image');
        $this->addSql('ALTER TABLE car DROP image');
        $this->addSql('ALTER TABLE moto DROP image');
    }
}
