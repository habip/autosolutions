<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151113204914 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CAR_SERVICES ADD LATITUDE VARCHAR(255) DEFAULT NULL, ADD LONGITUDE VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE TABLE SERVICE_GROUPS (ID INT AUTO_INCREMENT NOT NULL, NAME VARCHAR(255) NOT NULL, PRIMARY KEY(ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE SERVICES ADD GROUP_ID INT DEFAULT NULL');
        $this->addSql('ALTER TABLE SERVICES ADD CONSTRAINT FK_B5017D252A03EB FOREIGN KEY (GROUP_ID) REFERENCES SERVICE_GROUPS (ID)');
        $this->addSql('CREATE INDEX IDX_B5017D252A03EB ON SERVICES (GROUP_ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CAR_SERVICES DROP LATITUDE, DROP LONGITUDE');
        $this->addSql('ALTER TABLE SERVICES DROP FOREIGN KEY FK_B5017D252A03EB');
        $this->addSql('DROP TABLE SERVICE_GROUPS');
        $this->addSql('ALTER TABLE SERVICES DROP GROUP_ID');
    }
}
