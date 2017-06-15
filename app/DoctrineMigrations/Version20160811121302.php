<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160811121302 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE SUBSCRIBED_SERVICES ADD ORDERED_SERVICE_ID BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE SUBSCRIBED_SERVICES ADD CONSTRAINT FK_890ECE5A35111A2A FOREIGN KEY (ORDERED_SERVICE_ID) REFERENCES ORDERED_SERVICES (ID)');
        $this->addSql('CREATE INDEX IDX_890ECE5A35111A2A ON SUBSCRIBED_SERVICES (ORDERED_SERVICE_ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE SUBSCRIBED_SERVICES DROP FOREIGN KEY FK_890ECE5A35111A2A');
        $this->addSql('DROP INDEX IDX_890ECE5A35111A2A ON SUBSCRIBED_SERVICES');
        $this->addSql('ALTER TABLE SUBSCRIBED_SERVICES DROP ORDERED_SERVICE_ID');
    }
}
