<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150922153153 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE IMAGES CHANGE ADDED_TIMESTAMP ADDED_TIMESTAMP timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE CARS CHANGE BRAND_ID BRAND_ID BIGINT NOT NULL');
        $this->addSql('ALTER TABLE CAR_OWNER_REQUESTS CHANGE ADDED_TIMESTAMP ADDED_TIMESTAMP timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE STATUS STATUS enum(\'new\', \'assign\', \'reassign\', \'rejected\', \'canceled\', \'done\') NOT NULL DEFAULT \'new\'');
        $this->addSql('ALTER TABLE USERS CHANGE TYPE TYPE enum(\'company\', \'car_owner\') NOT NULL DEFAULT \'car_owner\', CHANGE LAST_VISIT LAST_VISIT timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE DIALOG CHANGE CREATION_TIMESTAMP CREATION_TIMESTAMP timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE MESSAGES CHANGE CREATED_TIMESTAMP CREATED_TIMESTAMP timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE MESSAGE_STATUS CHANGE DELIVERED_TIMESTAMP DELIVERED_TIMESTAMP timestamp NULL, CHANGE READ_TIMESTAMP READ_TIMESTAMP timestamp NULL, CHANGE CURRENT_STATUS CURRENT_STATUS enum(\'new\', \'delivered\', \'read\') NOT NULL DEFAULT \'new\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CARS CHANGE BRAND_ID BRAND_ID BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE CAR_OWNER_REQUESTS CHANGE STATUS STATUS VARCHAR(255) DEFAULT \'new\' NOT NULL COLLATE utf8_unicode_ci, CHANGE ADDED_TIMESTAMP ADDED_TIMESTAMP DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE DIALOG CHANGE CREATION_TIMESTAMP CREATION_TIMESTAMP DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE IMAGES CHANGE ADDED_TIMESTAMP ADDED_TIMESTAMP DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE MESSAGES CHANGE CREATED_TIMESTAMP CREATED_TIMESTAMP DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE MESSAGE_STATUS CHANGE DELIVERED_TIMESTAMP DELIVERED_TIMESTAMP DATETIME DEFAULT NULL, CHANGE READ_TIMESTAMP READ_TIMESTAMP DATETIME DEFAULT NULL, CHANGE CURRENT_STATUS CURRENT_STATUS VARCHAR(255) DEFAULT \'new\' NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE USERS CHANGE TYPE TYPE VARCHAR(255) DEFAULT \'car_owner\' NOT NULL COLLATE utf8_unicode_ci, CHANGE LAST_VISIT LAST_VISIT DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }
}
