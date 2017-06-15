<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160421142912 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE COMPANIES ADD AGENT_ID INT DEFAULT NULL');
        $this->addSql('ALTER TABLE COMPANIES ADD CONSTRAINT FK_C686B4D5CA6AABA7 FOREIGN KEY (AGENT_ID) REFERENCES AGENTS (ID)');
        $this->addSql('CREATE INDEX IDX_C686B4D5CA6AABA7 ON COMPANIES (AGENT_ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE COMPANIES DROP FOREIGN KEY FK_C686B4D5CA6AABA7');
        $this->addSql('DROP INDEX IDX_C686B4D5CA6AABA7 ON COMPANIES');
        $this->addSql('ALTER TABLE COMPANIES DROP AGENT_ID');
    }
}
