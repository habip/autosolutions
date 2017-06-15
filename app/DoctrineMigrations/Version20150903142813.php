<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150903142813 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE COMPANIES DROP QR_LOGIN, DROP QR_LOGIN_TIMESTAMP');
        $this->addSql('ALTER TABLE USERS ADD QR_LOGIN LONGTEXT DEFAULT NULL, ADD QR_LOGIN_TIMESTAMP DATETIME DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE COMPANIES ADD QR_LOGIN LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, ADD QR_LOGIN_TIMESTAMP DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE USERS DROP QR_LOGIN, DROP QR_LOGIN_TIMESTAMP');
    }
}
