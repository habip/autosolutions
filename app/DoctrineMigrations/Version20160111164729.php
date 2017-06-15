<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160111164729 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CARS ADD MODEL_ID INT DEFAULT NULL, DROP MODEL');
        $this->addSql('ALTER TABLE CARS ADD CONSTRAINT FK_A352F0A0870B6D4B FOREIGN KEY (MODEL_ID) REFERENCES CAR_MODELS (ID)');
        $this->addSql('CREATE INDEX IDX_A352F0A0870B6D4B ON CARS (MODEL_ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CARS DROP FOREIGN KEY FK_A352F0A0870B6D4B');
        $this->addSql('DROP INDEX IDX_A352F0A0870B6D4B ON CARS');
        $this->addSql('ALTER TABLE CARS ADD MODEL VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, DROP MODEL_ID');
    }
}
