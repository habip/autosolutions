<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160205115331 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE CAR_OWNER_REQUEST_REASONS (CAR_OWNER_REQUEST_ID BIGINT NOT NULL, REASON_ID INT NOT NULL, INDEX IDX_3000AA5F24AD0EDA (CAR_OWNER_REQUEST_ID), INDEX IDX_3000AA5F25344D9D (REASON_ID), PRIMARY KEY(CAR_OWNER_REQUEST_ID, REASON_ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE CAR_OWNER_REQUEST_REASONS ADD CONSTRAINT FK_3000AA5F24AD0EDA FOREIGN KEY (CAR_OWNER_REQUEST_ID) REFERENCES CAR_OWNER_REQUESTS (ID)');
        $this->addSql('ALTER TABLE CAR_OWNER_REQUEST_REASONS ADD CONSTRAINT FK_3000AA5F25344D9D FOREIGN KEY (REASON_ID) REFERENCES SERVICE_REASONS (ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE CAR_OWNER_REQUEST_REASONS');
    }
}
