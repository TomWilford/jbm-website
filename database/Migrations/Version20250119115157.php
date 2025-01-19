<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250119115157 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adding Bits table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('bits');
        $table->addColumn('id', Types::INTEGER, ['autoincrement' => true]);
        $table->addColumn('name', Types::STRING, ['notnull' => true]);
        $table->addColumn('code', Types::STRING, ['notnull' => true]);
        $table->addColumn('language', Types::STRING, ['notnull' => true]);
        $table->addColumn('description', Types::STRING, ['notnull' => false]);
        $table->addColumn('created_at', Types::DATE_IMMUTABLE, ['notnull' => true]);
        $table->addColumn('updated_at', Types::DATE_MUTABLE, ['notnull' => true]);
        $table->setPrimaryKey(['id']);
    }

    public function down(Schema $schema): void
    {
        if ($schema->hasTable('bits')) {
            $schema->dropTable('bits');
        }
    }
}
