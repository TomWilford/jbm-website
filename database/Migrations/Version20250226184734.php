<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250226184734 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Increase column sizes on Things & Bits.';
    }

    public function up(Schema $schema): void
    {
        $things = $schema->getTable('things');
        $things->getColumn('description')->setType(Type::getType(Types::TEXT));

        $bits = $schema->getTable('bits');
        $bits->getColumn('description')->setType(Type::getType(Types::TEXT));
        $bits->getColumn('code')->setType(Type::getType(Types::TEXT));
        $bits->getColumn('returns')->setType(Type::getType(Types::TEXT));
    }

    public function down(Schema $schema): void
    {
        $things = $schema->getTable('things');
        $things->getColumn('description')->setType(Type::getType(Types::STRING));

        $bits = $schema->getTable('bits');
        $bits->getColumn('description')->setType(Type::getType(Types::STRING));
        $bits->getColumn('code')->setType(Type::getType(Types::STRING));
        $bits->getColumn('returns')->setType(Type::getType(Types::STRING));
    }
}
