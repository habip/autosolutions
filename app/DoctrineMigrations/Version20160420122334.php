<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160420122334 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE AGENTS (ID INT AUTO_INCREMENT NOT NULL, FIRST_NAME VARCHAR(255) NOT NULL, LAST_NAME VARCHAR(255) NOT NULL, USER_ID BIGINT DEFAULT NULL, UNIQUE INDEX UNIQ_656E6CE8A0666B6F (USER_ID), PRIMARY KEY(ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE AGENTS ADD CONSTRAINT FK_656E6CE8A0666B6F FOREIGN KEY (USER_ID) REFERENCES USERS (ID)');
        $this->addSql('ALTER TABLE CAR_OWNERS CHANGE PHONE PHONE VARCHAR(35) DEFAULT NULL');
        $this->addSql('ALTER TABLE USERS ADD PHONE VARCHAR(35) DEFAULT NULL COMMENT \'(DC2Type:phone_number)\', CHANGE TYPE TYPE enum(\'company\', \'car_owner\', \'agent\') NOT NULL DEFAULT \'car_owner\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE AGENTS');
        $this->addSql('ALTER TABLE CAR_OWNERS CHANGE PHONE PHONE VARCHAR(35) NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:phone_number)\'');
        $this->addSql('ALTER TABLE USERS DROP PHONE, CHANGE TYPE TYPE enum(\'company\', \'car_owner\') NOT NULL DEFAULT \'car_owner\'');
    }
}
