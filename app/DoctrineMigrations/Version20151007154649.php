<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151007154649 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE CHANGES (ID CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE_ACTION enum(\'create\',\'update\',\'remove\') NOT NULL, OBJECT_CLASS VARCHAR(255) NOT NULL, OBJECT_ID BIGINT NOT NULL, CHANGE_TIMESTAMP timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE_VALUE LONGTEXT NOT NULL, PRIMARY KEY(ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE CHANGE_SUBSCRIBERS (ID BIGINT AUTO_INCREMENT NOT NULL, SYNC_SEQ INT DEFAULT NULL, CHANGE_ID CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', USER_ID BIGINT NOT NULL, INDEX IDX_28A8C31A5DB3D3FB (CHANGE_ID), INDEX IDX_28A8C31AA0666B6F (USER_ID), PRIMARY KEY(ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE CHANGE_SUBSCRIBERS ADD CONSTRAINT FK_28A8C31A5DB3D3FB FOREIGN KEY (CHANGE_ID) REFERENCES CHANGES (ID)');
        $this->addSql('ALTER TABLE CHANGE_SUBSCRIBERS ADD CONSTRAINT FK_28A8C31AA0666B6F FOREIGN KEY (USER_ID) REFERENCES USERS (ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CHANGE_SUBSCRIBERS DROP FOREIGN KEY FK_28A8C31A5DB3D3FB');
        $this->addSql('DROP TABLE CHANGES');
        $this->addSql('DROP TABLE CHANGE_SUBSCRIBERS');
    }
}
