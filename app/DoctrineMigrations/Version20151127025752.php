<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151127025752 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE NOTIFICATIONS (ID BIGINT AUTO_INCREMENT NOT NULL, TYPE enum(\'single\', \'multiple\') NOT NULL DEFAULT \'single\', ACTIONS LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', CHOICES LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', MESSAGE LONGTEXT NOT NULL, AUTHOR VARCHAR(255) DEFAULT NULL, REQUEST_ID BIGINT NOT NULL, INDEX IDX_464CB23671ACE37A (REQUEST_ID), PRIMARY KEY(ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE NOTIFICATION_RESPONSES (ID BIGINT AUTO_INCREMENT NOT NULL, ACTION VARCHAR(255) NOT NULL, CHOICES LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', NOTIFICATION_ID BIGINT NOT NULL, INDEX IDX_FCF3FF0BB4BA9FC7 (NOTIFICATION_ID), PRIMARY KEY(ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE NOTIFICATIONS ADD CONSTRAINT FK_464CB23671ACE37A FOREIGN KEY (REQUEST_ID) REFERENCES CAR_OWNER_REQUESTS (ID)');
        $this->addSql('ALTER TABLE NOTIFICATION_RESPONSES ADD CONSTRAINT FK_FCF3FF0BB4BA9FC7 FOREIGN KEY (NOTIFICATION_ID) REFERENCES NOTIFICATIONS (ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE NOTIFICATION_RESPONSES DROP FOREIGN KEY FK_FCF3FF0BB4BA9FC7');
        $this->addSql('DROP TABLE NOTIFICATIONS');
        $this->addSql('DROP TABLE NOTIFICATION_RESPONSES');
    }
}
