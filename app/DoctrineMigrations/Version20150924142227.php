<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150924142227 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ORGANIZATION_INFOS CHANGE KPP KPP VARCHAR(9) DEFAULT NULL, CHANGE BANK_ACCOUNT_NUMBER BANK_ACCOUNT_NUMBER VARCHAR(20) DEFAULT NULL, CHANGE CORRESPONDENT_NUMBER CORRESPONDENT_NUMBER VARCHAR(20) DEFAULT NULL, CHANGE BANK_CODE BANK_CODE VARCHAR(9) DEFAULT NULL, CHANGE BANK BANK VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ORGANIZATION_INFOS CHANGE KPP KPP VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci, CHANGE BANK_ACCOUNT_NUMBER BANK_ACCOUNT_NUMBER VARCHAR(20) NOT NULL COLLATE utf8_unicode_ci, CHANGE CORRESPONDENT_NUMBER CORRESPONDENT_NUMBER VARCHAR(20) NOT NULL COLLATE utf8_unicode_ci, CHANGE BANK_CODE BANK_CODE VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci, CHANGE BANK BANK VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
    }
}
