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
final class Version20260522222245 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adding snaps table';
    }

    public function up(Schema $schema): void
    {
        if (!$schema->hasTable('snaps')) {
            $table = $schema->createTable('snaps');
            $table->addColumn('id', Types::INTEGER, ['autoincrement' => true]);
            $table->addColumn('album_id', Types::INTEGER, ['notnull' => true]);
            $table->addColumn('image', Types::BLOB, ['notnull' => true]);
            $table->addColumn('mime_type', Types::STRING, ['notnull' => true]);
            $table->addColumn('created_at', Types::INTEGER, ['notnull' => true]);
            $table->addColumn('updated_at', Types::INTEGER, ['notnull' => true]);

            $table->addPrimaryKeyConstraint(
                PrimaryKeyConstraint::editor()
                    ->setColumnNames(UnqualifiedName::unquoted('id'))
                    ->create()
            );
        }
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('snaps');
    }
}
