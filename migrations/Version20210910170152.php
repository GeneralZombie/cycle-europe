<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210910170152 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tour DROP duration');
        $this->addSql('ALTER TABLE tour_collection DROP started_at, DROP finished_at, DROP distance, DROP elevation_gain, DROP elevation_loss, DROP duration');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tour ADD duration INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tour_collection ADD started_at DATE DEFAULT NULL, ADD finished_at DATE DEFAULT NULL, ADD distance INT NOT NULL, ADD elevation_gain INT NOT NULL, ADD elevation_loss INT NOT NULL, ADD duration INT DEFAULT NULL');
    }
}
