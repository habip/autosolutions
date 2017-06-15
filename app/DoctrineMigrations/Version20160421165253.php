<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160421165253 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE AGENT_CLIENTS (ID INT AUTO_INCREMENT NOT NULL, AGENT_ID INT DEFAULT NULL, INDEX IDX_DA12C6DCA6AABA7 (AGENT_ID), PRIMARY KEY(ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE AGENT_CLIENTS ADD CONSTRAINT FK_DA12C6DCA6AABA7 FOREIGN KEY (AGENT_ID) REFERENCES AGENTS (ID)');
        $this->addSql('ALTER TABLE COMPANIES DROP FOREIGN KEY FK_C686B4D5CA6AABA7');
        $this->addSql('DROP INDEX IDX_C686B4D5CA6AABA7 ON COMPANIES');
        $this->addSql('ALTER TABLE COMPANIES CHANGE agent_id AGENT_CLIENT_ID INT DEFAULT NULL');
        $this->addSql('ALTER TABLE COMPANIES ADD CONSTRAINT FK_C686B4D5FA42EEF6 FOREIGN KEY (AGENT_CLIENT_ID) REFERENCES AGENT_CLIENTS (ID)');
        $this->addSql('CREATE INDEX IDX_C686B4D5FA42EEF6 ON COMPANIES (AGENT_CLIENT_ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE COMPANIES DROP FOREIGN KEY FK_C686B4D5FA42EEF6');
        $this->addSql('DROP TABLE AGENT_CLIENTS');
        $this->addSql('DROP INDEX IDX_C686B4D5FA42EEF6 ON COMPANIES');
        $this->addSql('ALTER TABLE COMPANIES CHANGE agent_client_id AGENT_ID INT DEFAULT NULL');
        $this->addSql('ALTER TABLE COMPANIES ADD CONSTRAINT FK_C686B4D5CA6AABA7 FOREIGN KEY (AGENT_ID) REFERENCES AGENTS (ID)');
        $this->addSql('CREATE INDEX IDX_C686B4D5CA6AABA7 ON COMPANIES (AGENT_ID)');
        
    }
}
