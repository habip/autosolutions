<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150914155545 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE REQUESTS (ID BIGINT AUTO_INCREMENT NOT NULL, NAME VARCHAR(255) NOT NULL, DESCRIPTION LONGTEXT DEFAULT NULL, DIALOG_ID BIGINT NOT NULL, COMPANY_ID BIGINT NOT NULL, CAR_OWNER_ID BIGINT NOT NULL, INDEX IDX_4D27185A4494109 (COMPANY_ID), INDEX IDX_4D27185DE84D9AE (CAR_OWNER_ID), PRIMARY KEY(ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE REQUESTS ADD CONSTRAINT FK_4D27185A4494109 FOREIGN KEY (COMPANY_ID) REFERENCES COMPANIES (ID)');
        $this->addSql('ALTER TABLE REQUESTS ADD CONSTRAINT FK_4D27185DE84D9AE FOREIGN KEY (CAR_OWNER_ID) REFERENCES CAR_OWNERS (ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE REQUESTS');
    }
}
