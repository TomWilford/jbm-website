<?php

declare(strict_types=1);

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241207173509 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adding Things table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('things');
        $table->addColumn('id', Types::INTEGER, ['autoincrement' => true]);
        $table->addColumn('name', Types::STRING, ['notnull' => true]);
        $table->addColumn('short_description', Types::STRING, ['notnull' => true]);
        $table->addColumn('description', Types::STRING, ['notnull' => true]);
        $table->addColumn('image', Types::STRING, ['notnull' => false]);
        $table->addColumn('url', Types::STRING, ['notnull' => false]);
        $table->addColumn('fault_level', Types::STRING, ['notnull' => true]);
        $table->addColumn('from', Types::DATE_IMMUTABLE, ['notnull' => true]);
        $table->addColumn('to', Types::DATE_MUTABLE, ['notnull' => false]);
        $table->addColumn('created_at', Types::DATE_IMMUTABLE, ['notnull' => true]);
        $table->addColumn('updated_at', Types::DATE_MUTABLE, ['notnull' => true]);
        $table->setPrimaryKey(['id']);
    }

    public function down(Schema $schema): void
    {
        if ($schema->hasTable('things')) {
            $schema->dropTable('things');
        }
    }
}
