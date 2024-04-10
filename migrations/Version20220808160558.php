<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220808160558 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE record_altitude ADD tour_id INT DEFAULT NULL, ADD title VARCHAR(255) DEFAULT NULL, ADD date DATE DEFAULT NULL, ADD distance INT NOT NULL, ADD elevation_gain INT NOT NULL, ADD elevation_loss INT NOT NULL');
        $this->addSql('ALTER TABLE record_altitude ADD CONSTRAINT FK_95C977B315ED8D43 FOREIGN KEY (tour_id) REFERENCES tour (id)');
        $this->addSql('CREATE INDEX IDX_95C977B315ED8D43 ON record_altitude (tour_id)');
        $this->addSql('ALTER TABLE record_distance ADD tour_id INT DEFAULT NULL, ADD title VARCHAR(255) DEFAULT NULL, ADD date DATE DEFAULT NULL, ADD distance INT NOT NULL, ADD elevation_gain INT NOT NULL, ADD elevation_loss INT NOT NULL');
        $this->addSql('ALTER TABLE record_distance ADD CONSTRAINT FK_3291480015ED8D43 FOREIGN KEY (tour_id) REFERENCES tour (id)');
        $this->addSql('CREATE INDEX IDX_3291480015ED8D43 ON record_distance (tour_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE record_altitude DROP FOREIGN KEY FK_95C977B315ED8D43');
        $this->addSql('DROP INDEX IDX_95C977B315ED8D43 ON record_altitude');
        $this->addSql('ALTER TABLE record_altitude DROP tour_id, DROP title, DROP date, DROP distance, DROP elevation_gain, DROP elevation_loss');
        $this->addSql('ALTER TABLE record_distance DROP FOREIGN KEY FK_3291480015ED8D43');
        $this->addSql('DROP INDEX IDX_3291480015ED8D43 ON record_distance');
        $this->addSql('ALTER TABLE record_distance DROP tour_id, DROP title, DROP date, DROP distance, DROP elevation_gain, DROP elevation_loss');
    }
}
