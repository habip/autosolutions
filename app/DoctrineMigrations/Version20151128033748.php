<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151128033748 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE NOTIFICATIONS ADD CURRENT_STATUS enum(\'new\', \'read\') NOT NULL DEFAULT \'new\'');
        $this->addSql('ALTER TABLE NOTIFICATIONS ADD USER_ID BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE NOTIFICATIONS ADD CREATED_TIMESTAMP timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE NOTIFICATIONS ADD CONSTRAINT FK_464CB236A0666B6F FOREIGN KEY (USER_ID) REFERENCES USERS (ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE NOTIFICATIONS DROP FOREIGN KEY FK_464CB236A0666B6F');
        $this->addSql('ALTER TABLE NOTIFICATIONS DROP CURRENT_STATUS, DROP USER_ID, DROP CREATED_TIMESTAMP');
    }
}
