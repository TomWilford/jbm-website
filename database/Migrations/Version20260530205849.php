<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260530205849 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Set unique starting points for albums and snaps';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO sqlite_sequence (name, seq) VALUES ('albums', 999)");
        $this->addSql("INSERT INTO sqlite_sequence (name, seq) VALUES ('snaps', 1999)");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM SQLITE_SEQUENCE WHERE name='albums'");
        $this->addSql("DELETE FROM SQLITE_SEQUENCE WHERE name='snaps'");
    }
}
