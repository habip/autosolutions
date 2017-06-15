<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160606150735 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CARS ADD VIN VARCHAR(20) DEFAULT NULL, ADD ENGINE_VOLUME NUMERIC(10, 3) DEFAULT NULL, ADD ENGINE_TYPE VARCHAR(20) DEFAULT NULL, ADD BODY_TYPE VARCHAR(20) DEFAULT NULL, ADD DRIVE_TYPE VARCHAR(20) DEFAULT NULL, ADD CAR_TRAIDER VARCHAR(100) DEFAULT NULL, ADD SALE_DATE DATETIME DEFAULT NULL, ADD COLOR VARCHAR(20) DEFAULT NULL, ADD OWNER VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE CAR_OWNER_REQUESTS ADD CHECK_IN_DATE_TIME DATETIME DEFAULT NULL, ADD CHECK_OUT_DATE_TIME DATETIME DEFAULT NULL, ADD MASTER_INSPECTOR VARCHAR(100) DEFAULT NULL, CHANGE ADDED_TIMESTAMP ADDED_TIMESTAMP timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CARS DROP VIN, DROP ENGINE_VOLUME, DROP ENGINE_TYPE, DROP BODY_TYPE, DROP DRIVE_TYPE, DROP CAR_TRAIDER, DROP SALE_DATE, DROP COLOR, DROP OWNER');
        $this->addSql('ALTER TABLE CAR_OWNER_REQUESTS DROP CHECK_IN_DATE_TIME, DROP CHECK_OUT_DATE_TIME, DROP MASTER_INSPECTOR');
    }
}
