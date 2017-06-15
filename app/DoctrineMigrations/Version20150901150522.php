<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150901150522 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE IMAGES ADD USER_ID BIGINT DEFAULT NULL, DROP OWNER_TYPE, DROP OWNER_ID');
        $this->addSql('ALTER TABLE IMAGES ADD CONSTRAINT FK_10E779ECA0666B6F FOREIGN KEY (USER_ID) REFERENCES USERS (ID)');
        $this->addSql('CREATE INDEX IDX_10E779ECA0666B6F ON IMAGES (USER_ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE IMAGES DROP FOREIGN KEY FK_10E779ECA0666B6F');
        $this->addSql('DROP INDEX IDX_10E779ECA0666B6F ON IMAGES');
        $this->addSql('ALTER TABLE IMAGES ADD OWNER_TYPE VARCHAR(20) NOT NULL COLLATE utf8_unicode_ci, ADD OWNER_ID BIGINT NOT NULL, DROP USER_ID');
    }
}
