<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160318190423 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE CAR_OWNER_REQUEST_ITEMS (ID BIGINT AUTO_INCREMENT NOT NULL, TYPE enum(\'works\', \'parts\') NOT NULL DEFAULT \'works\', NAME VARCHAR(255) NOT NULL, QUANTITY DOUBLE PRECISION NOT NULL, SUM NUMERIC(10, 2) NOT NULL, REQUEST_ID BIGINT NOT NULL, INDEX IDX_D846B2B971ACE37A (REQUEST_ID), PRIMARY KEY(ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE CAR_OWNER_REQUEST_ITEMS ADD CONSTRAINT FK_D846B2B971ACE37A FOREIGN KEY (REQUEST_ID) REFERENCES CAR_OWNER_REQUESTS (ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE CAR_OWNER_REQUEST_ITEMS');
    }
}
