<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210811214355 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE magasin ADD owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE magasin ADD CONSTRAINT FK_54AF5F277E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_54AF5F277E3C61F9 ON magasin (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE magasin DROP FOREIGN KEY FK_54AF5F277E3C61F9');
        $this->addSql('DROP INDEX UNIQ_54AF5F277E3C61F9 ON magasin');
        $this->addSql('ALTER TABLE magasin DROP owner_id');
    }
}
