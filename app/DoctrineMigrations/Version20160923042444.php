<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160923042444 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE SERVICE_COSTS (ID BIGINT AUTO_INCREMENT NOT NULL, NAME VARCHAR(255) NOT NULL, COST NUMERIC(10, 2) NOT NULL, DURATION INT NOT NULL, CREATED_TIMESTAMP timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, SERVICE_ID BIGINT DEFAULT NULL, COMPANY_ID BIGINT DEFAULT NULL, VEHICLE_TYPE_ID INT DEFAULT NULL, INDEX IDX_278FBD9CDE8EF239 (SERVICE_ID), INDEX IDX_278FBD9CA4494109 (COMPANY_ID), INDEX IDX_278FBD9C78E9B1E9 (VEHICLE_TYPE_ID), PRIMARY KEY(ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VEHICLE_TYPES (ID INT AUTO_INCREMENT NOT NULL, NAME VARCHAR(150) NOT NULL, CREATED_TIMESTAMP timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE SERVICE_COSTS ADD CONSTRAINT FK_278FBD9CDE8EF239 FOREIGN KEY (SERVICE_ID) REFERENCES SERVICES (ID)');
        $this->addSql('ALTER TABLE SERVICE_COSTS ADD CONSTRAINT FK_278FBD9CA4494109 FOREIGN KEY (COMPANY_ID) REFERENCES COMPANIES (ID)');
        $this->addSql('ALTER TABLE SERVICE_COSTS ADD CONSTRAINT FK_278FBD9C78E9B1E9 FOREIGN KEY (VEHICLE_TYPE_ID) REFERENCES VEHICLE_TYPES (ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE SERVICE_COSTS DROP FOREIGN KEY FK_278FBD9C78E9B1E9');
        $this->addSql('DROP TABLE SERVICE_COSTS');
        $this->addSql('DROP TABLE VEHICLE_TYPES');
    }
}
