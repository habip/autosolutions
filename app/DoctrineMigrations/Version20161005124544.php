<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161005124544 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CAR_MODELS ADD VEHICLE_TYPE_ID INT DEFAULT NULL, DROP VEHICLE_TYPE');
        $this->addSql('ALTER TABLE CAR_MODELS ADD CONSTRAINT FK_389CF85878E9B1E9 FOREIGN KEY (VEHICLE_TYPE_ID) REFERENCES VEHICLE_TYPES (ID)');
        $this->addSql('CREATE INDEX IDX_389CF85878E9B1E9 ON CAR_MODELS (VEHICLE_TYPE_ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CAR_MODELS DROP FOREIGN KEY FK_389CF85878E9B1E9');
        $this->addSql('DROP INDEX IDX_389CF85878E9B1E9 ON CAR_MODELS');
        $this->addSql('ALTER TABLE CAR_MODELS ADD VEHICLE_TYPE VARCHAR(150) DEFAULT NULL COLLATE utf8_unicode_ci, DROP VEHICLE_TYPE_ID');
    }
}
