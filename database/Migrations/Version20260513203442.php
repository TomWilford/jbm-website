<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use Doctrine\DBAL\Schema\Name\UnqualifiedName;
use Doctrine\DBAL\Schema\PrimaryKeyConstraint;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260513203442 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adding albums table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('albums');

        $table->addColumn('id', Types::INTEGER, ['autoincrement' => true]);
        $table->addColumn('name', Types::STRING, ['notnull' => true]);
        $table->addColumn('camera', Types::STRING, ['notnull' => true]);
        $table->addColumn('location', Types::STRING, ['notnull' => true]);
        $table->addColumn('date', Types::STRING, ['notnull' => true]);
        $table->addColumn('created_at', Types::INTEGER, ['notnull' => false]);
        $table->addColumn('updated_at', Types::INTEGER, ['notnull' => false]);

        $table->addPrimaryKeyConstraint(
            PrimaryKeyConstraint::editor()
                ->setColumnNames(UnqualifiedName::unquoted('id'))
                ->create()
        );
    }

    public function down(Schema $schema): void
    {
        if ($schema->hasTable('albums')) {
            $schema->dropTable('albums');
        }
    }
}
