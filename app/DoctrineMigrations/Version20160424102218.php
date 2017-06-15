<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160424102218 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE AGENTS ADD NAME VARCHAR(255) DEFAULT NULL, ADD ADDRESS VARCHAR(255) DEFAULT NULL, ADD PHONE VARCHAR(255) DEFAULT NULL, ADD EMAIL VARCHAR(255) DEFAULT NULL, ADD DESCRIPTION LONGTEXT DEFAULT NULL, ADD FULL_NAME VARCHAR(255) DEFAULT NULL, ADD INN VARCHAR(12) DEFAULT NULL, ADD BANK VARCHAR(255) DEFAULT NULL, ADD BANK_ACCOUNT_NUMBER VARCHAR(20) DEFAULT NULL, ADD CORRESPONDENT_NUMBER VARCHAR(20) DEFAULT NULL, ADD LOCALITY_ID INT DEFAULT NULL, ADD LEGAL_FORM_ID BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE AGENTS ADD CONSTRAINT FK_656E6CE83DCCEE9A FOREIGN KEY (LOCALITY_ID) REFERENCES LOCALITIES (ID)');
        $this->addSql('ALTER TABLE AGENTS ADD CONSTRAINT FK_656E6CE87FBA2340 FOREIGN KEY (LEGAL_FORM_ID) REFERENCES LEGAL_FORMS (ID)');
        $this->addSql('CREATE INDEX IDX_656E6CE83DCCEE9A ON AGENTS (LOCALITY_ID)');
        $this->addSql('CREATE INDEX IDX_656E6CE87FBA2340 ON AGENTS (LEGAL_FORM_ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE AGENTS DROP FOREIGN KEY FK_656E6CE83DCCEE9A');
        $this->addSql('ALTER TABLE AGENTS DROP FOREIGN KEY FK_656E6CE87FBA2340');
        $this->addSql('DROP INDEX IDX_656E6CE83DCCEE9A ON AGENTS');
        $this->addSql('DROP INDEX IDX_656E6CE87FBA2340 ON AGENTS');
        $this->addSql('ALTER TABLE AGENTS DROP NAME, DROP ADDRESS, DROP PHONE, DROP EMAIL, DROP DESCRIPTION, DROP FULL_NAME, DROP INN, DROP BANK, DROP BANK_ACCOUNT_NUMBER, DROP CORRESPONDENT_NUMBER, DROP LOCALITY_ID, DROP LEGAL_FORM_ID');
    }
}
