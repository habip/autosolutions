<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150828150051 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE COMPANIES DROP FOREIGN KEY FK_C686B4D5C3DBFFC1');
        $this->addSql('DROP INDEX IDX_C686B4D5C3DBFFC1 ON COMPANIES');
        $this->addSql('ALTER TABLE COMPANIES DROP IMAGE_ID');
        $this->addSql('ALTER TABLE USERS ADD IMAGE_ID BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE USERS ADD CONSTRAINT FK_E3D76759C3DBFFC1 FOREIGN KEY (IMAGE_ID) REFERENCES IMAGES (ID)');
        $this->addSql('CREATE INDEX IDX_E3D76759C3DBFFC1 ON USERS (IMAGE_ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE COMPANIES ADD IMAGE_ID BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE COMPANIES ADD CONSTRAINT FK_C686B4D5C3DBFFC1 FOREIGN KEY (IMAGE_ID) REFERENCES IMAGES (ID)');
        $this->addSql('CREATE INDEX IDX_C686B4D5C3DBFFC1 ON COMPANIES (IMAGE_ID)');
        $this->addSql('ALTER TABLE USERS DROP FOREIGN KEY FK_E3D76759C3DBFFC1');
        $this->addSql('DROP INDEX IDX_E3D76759C3DBFFC1 ON USERS');
        $this->addSql('ALTER TABLE USERS DROP IMAGE_ID');
    }
}
