<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211225152131 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE widget (id INT AUTO_INCREMENT NOT NULL, start_time VARCHAR(12) NOT NULL, end_time VARCHAR(12) NOT NULL, position INT NOT NULL, size SMALLINT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE widget_sensor (widget_id INT NOT NULL, sensor_id INT NOT NULL, INDEX IDX_4809E4B8FBE885E2 (widget_id), INDEX IDX_4809E4B8A247991F (sensor_id), PRIMARY KEY(widget_id, sensor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE widget_sensor ADD CONSTRAINT FK_4809E4B8FBE885E2 FOREIGN KEY (widget_id) REFERENCES widget (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE widget_sensor ADD CONSTRAINT FK_4809E4B8A247991F FOREIGN KEY (sensor_id) REFERENCES sensor (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE widget_sensor DROP FOREIGN KEY FK_4809E4B8FBE885E2');
        $this->addSql('DROP TABLE widget');
        $this->addSql('DROP TABLE widget_sensor');
    }
}
