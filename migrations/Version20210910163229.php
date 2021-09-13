<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210910163229 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tour_collection (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, subtitle VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, started_at DATE DEFAULT NULL, finished_at DATE DEFAULT NULL, distance INT NOT NULL, elevation_gain INT NOT NULL, elevation_loss INT NOT NULL, duration INT DEFAULT NULL, UNIQUE INDEX UNIQ_1A9173AD989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tour_collection_tour (tour_collection_id INT NOT NULL, tour_id INT NOT NULL, INDEX IDX_8A35F53B34FB82FE (tour_collection_id), INDEX IDX_8A35F53B15ED8D43 (tour_id), PRIMARY KEY(tour_collection_id, tour_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tour_collection_tour ADD CONSTRAINT FK_8A35F53B34FB82FE FOREIGN KEY (tour_collection_id) REFERENCES tour_collection (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tour_collection_tour ADD CONSTRAINT FK_8A35F53B15ED8D43 FOREIGN KEY (tour_id) REFERENCES tour (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tour_collection_tour DROP FOREIGN KEY FK_8A35F53B34FB82FE');
        $this->addSql('DROP TABLE tour_collection');
        $this->addSql('DROP TABLE tour_collection_tour');
    }
}
