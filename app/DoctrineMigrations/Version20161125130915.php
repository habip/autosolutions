<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161125130915 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CAR_OWNER_REQUEST_ITEMS ADD COST NUMERIC(10, 2) NOT NULL, ADD COMPANY_SERVICE_ID BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE CAR_OWNER_REQUEST_ITEMS ADD CONSTRAINT FK_D846B2B9E9343C53 FOREIGN KEY (COMPANY_SERVICE_ID) REFERENCES COMPANY_SERVICES (ID)');
        $this->addSql('CREATE INDEX IDX_D846B2B9E9343C53 ON CAR_OWNER_REQUEST_ITEMS (COMPANY_SERVICE_ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CAR_OWNER_REQUEST_ITEMS DROP FOREIGN KEY FK_D846B2B9E9343C53');
        $this->addSql('DROP INDEX IDX_D846B2B9E9343C53 ON CAR_OWNER_REQUEST_ITEMS');
        $this->addSql('ALTER TABLE CAR_OWNER_REQUEST_ITEMS DROP COST, DROP COMPANY_SERVICE_ID');
    }
}
