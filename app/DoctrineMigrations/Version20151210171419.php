<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151210171419 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE DISTRICTS (ID INT AUTO_INCREMENT NOT NULL, NAME VARCHAR(150) NOT NULL, LOCALITY_ID INT NOT NULL, INDEX IDX_2C2106333DCCEE9A (LOCALITY_ID), PRIMARY KEY(ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE DISTRICTS ADD CONSTRAINT FK_2C2106333DCCEE9A FOREIGN KEY (LOCALITY_ID) REFERENCES LOCALITIES (ID)');
        $this->addSql('CREATE TABLE CAR_MODELS (ID INT AUTO_INCREMENT NOT NULL, NAME VARCHAR(150) NOT NULL, BRAND_ID BIGINT NOT NULL, INDEX IDX_389CF858BA8B0AA4 (BRAND_ID), PRIMARY KEY(ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE CAR_MODELS ADD CONSTRAINT FK_389CF858BA8B0AA4 FOREIGN KEY (BRAND_ID) REFERENCES BRANDS (ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE DISTRICTS');
        $this->addSql('DROP TABLE CAR_MODELS');
    }
}
