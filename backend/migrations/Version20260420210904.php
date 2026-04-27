<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260420210904 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE agent DROP CONSTRAINT fk_268b9c9d7e3c61f9');
        $this->addSql('DROP INDEX idx_268b9c9d7e3c61f9');
        $this->addSql('ALTER TABLE agent RENAME COLUMN owner_id TO created_by_id');
        $this->addSql('ALTER TABLE agent ADD CONSTRAINT FK_268B9C9DB03A8386 FOREIGN KEY (created_by_id) REFERENCES "user" (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_268B9C9DB03A8386 ON agent (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE agent DROP CONSTRAINT FK_268B9C9DB03A8386');
        $this->addSql('DROP INDEX IDX_268B9C9DB03A8386');
        $this->addSql('ALTER TABLE agent RENAME COLUMN created_by_id TO owner_id');
        $this->addSql('ALTER TABLE agent ADD CONSTRAINT fk_268b9c9d7e3c61f9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_268b9c9d7e3c61f9 ON agent (owner_id)');
    }
}
