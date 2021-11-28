<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211128105149 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sensor (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, key_code VARCHAR(255) NOT NULL, unit VARCHAR(8) DEFAULT NULL, actif TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sensor_data (id INT AUTO_INCREMENT NOT NULL, sensor_id INT NOT NULL, value DOUBLE PRECISION NOT NULL, date DATETIME NOT NULL, INDEX IDX_801762CCA247991F (sensor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sensor_data ADD CONSTRAINT FK_801762CCA247991F FOREIGN KEY (sensor_id) REFERENCES sensor (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sensor_data DROP FOREIGN KEY FK_801762CCA247991F');
        $this->addSql('DROP TABLE sensor');
        $this->addSql('DROP TABLE sensor_data');
    }
}
