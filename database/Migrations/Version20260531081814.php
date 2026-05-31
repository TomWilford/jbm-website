<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260531081814 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Give albums a date property for sorting';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->getTable('albums');
        if (!$table->hasColumn('sort_date')) {
            $table->addColumn('sort_date', Types::INTEGER, ['notnull' => true]);
        }
    }

    public function down(Schema $schema): void
    {
        $table = $schema->getTable('albums');
        $table->dropColumn('sort_date');
    }
}
