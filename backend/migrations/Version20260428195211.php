<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260428195211 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task_run ADD final_prompt TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE task_run ADD error_message TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE task_run ADD started_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE task_run ADD finished_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task_run DROP final_prompt');
        $this->addSql('ALTER TABLE task_run DROP error_message');
        $this->addSql('ALTER TABLE task_run DROP started_at');
        $this->addSql('ALTER TABLE task_run DROP finished_at');
    }
}
