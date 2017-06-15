<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151021163423 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CHANGES ADD SYNC_NUM BIGINT DEFAULT NULL');
        $this->addSql('CREATE TABLE WORKPLACES (ID BIGINT AUTO_INCREMENT NOT NULL, AUTH_TOKEN VARCHAR(255) DEFAULT NULL, DESCRIPTION LONGTEXT DEFAULT NULL, COMPANY_ID BIGINT DEFAULT NULL, INDEX IDX_57F3E6D9A4494109 (COMPANY_ID), PRIMARY KEY(ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE WORKPLACES ADD CONSTRAINT FK_57F3E6D9A4494109 FOREIGN KEY (COMPANY_ID) REFERENCES COMPANIES (ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CHANGES DROP SYNC_NUM');
        $this->addSql('DROP TABLE WORKPLACES');
    }
}
