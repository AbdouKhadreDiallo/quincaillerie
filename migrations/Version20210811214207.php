<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210811214207 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE magasin DROP FOREIGN KEY FK_54AF5F2755B127A4');
        $this->addSql('ALTER TABLE magasin DROP FOREIGN KEY FK_54AF5F277E3C61F9');
        $this->addSql('DROP INDEX IDX_54AF5F2755B127A4 ON magasin');
        $this->addSql('DROP INDEX UNIQ_54AF5F277E3C61F9 ON magasin');
        $this->addSql('ALTER TABLE magasin ADD is_blocked TINYINT(1) DEFAULT NULL, DROP added_by_id, DROP owner_id');
        $this->addSql('ALTER TABLE user DROP magasin_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE magasin ADD added_by_id INT NOT NULL, ADD owner_id INT NOT NULL, DROP is_blocked');
        $this->addSql('ALTER TABLE magasin ADD CONSTRAINT FK_54AF5F2755B127A4 FOREIGN KEY (added_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE magasin ADD CONSTRAINT FK_54AF5F277E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_54AF5F2755B127A4 ON magasin (added_by_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_54AF5F277E3C61F9 ON magasin (owner_id)');
        $this->addSql('ALTER TABLE user ADD magasin_id INT NOT NULL');
    }
}
