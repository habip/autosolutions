<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160215151310 extends AbstractMigration
{
    public function preUp(Schema $schema)
    {
        $this->connection->exec('delete from CHANGE_SUBSCRIBERS');
        $this->connection->exec('delete from CHANGES');
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CHANGE_SUBSCRIBERS DROP FOREIGN KEY FK_28A8C31A5DB3D3FB');
        $this->addSql('ALTER TABLE CHANGES ADD GUID CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', DROP SYNC_NUM, CHANGE ID ID BIGINT AUTO_INCREMENT NOT NULL ');
        $this->addSql('ALTER TABLE CHANGE_SUBSCRIBERS DROP SYNC_SEQ, CHANGE CHANGE_ID CHANGE_ID BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE CHANGE_SUBSCRIBERS ADD CONSTRAINT FK_28A8C31A5DB3D3FB FOREIGN KEY (CHANGE_ID) REFERENCES CHANGES (ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CHANGE_SUBSCRIBERS DROP FOREIGN KEY FK_28A8C31A5DB3D3FB');
        $this->addSql('ALTER TABLE CHANGES ADD SYNC_NUM BIGINT DEFAULT NULL, DROP GUID, CHANGE ID ID CHAR(36) NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE CHANGE_SUBSCRIBERS ADD SYNC_SEQ INT DEFAULT NULL, CHANGE CHANGE_ID CHANGE_ID CHAR(36) DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE CHANGE_SUBSCRIBERS ADD CONSTRAINT FK_28A8C31A5DB3D3FB FOREIGN KEY (CHANGE_ID) REFERENCES CHANGES (ID)');
    }
}
