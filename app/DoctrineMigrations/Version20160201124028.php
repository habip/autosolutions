<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160201124028 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE DICTIONARY_ITEMS (ID INT AUTO_INCREMENT NOT NULL, NAME VARCHAR(255) NOT NULL, DICTIONARY_ID INT DEFAULT NULL, INDEX IDX_73886CF5B15F1F39 (DICTIONARY_ID), PRIMARY KEY(ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE DICTIONARIES (ID INT AUTO_INCREMENT NOT NULL, NAME VARCHAR(255) NOT NULL, PRIMARY KEY(ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE DICTIONARY_ITEMS ADD CONSTRAINT FK_73886CF5B15F1F39 FOREIGN KEY (DICTIONARY_ID) REFERENCES DICTIONARIES (ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE DICTIONARY_ITEMS DROP FOREIGN KEY FK_73886CF5B15F1F39');
        $this->addSql('DROP TABLE DICTIONARY_ITEMS');
        $this->addSql('DROP TABLE DICTIONARIES');
    }
}
