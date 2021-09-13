<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210908134311 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tour ADD active TINYINT(1) NOT NULL, ADD started_at DATE NOT NULL, ADD finished_at DATE NOT NULL, ADD hide_in_list TINYINT(1) NOT NULL, DROP start, DROP end');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6AD1F969989D9B62 ON tour (slug)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_6AD1F969989D9B62 ON tour');
        $this->addSql('ALTER TABLE tour ADD start DATE NOT NULL, ADD end DATE NOT NULL, DROP active, DROP started_at, DROP finished_at, DROP hide_in_list');
    }
}
