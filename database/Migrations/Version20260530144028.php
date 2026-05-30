<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260530144028 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adding image orientation column to the snaps table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->getTable('snaps');
        if (!$table->hasColumn('orientation')) {
            $table->addColumn('orientation', 'string', ['notnull' => true, 'length' => 16]);
        }
    }

    public function down(Schema $schema): void
    {
        $table = $schema->getTable('snaps');
        if ($table->hasColumn('orientation')) {
            $table->dropColumn('orientation');
        }
    }
}
