<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150917142413 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CARS ADD BRAND_ID BIGINT DEFAULT NULL');
        $this->addSql('CREATE INDEX FK_A352F0A0BA2344A9 ON BRANDS (ID)');
        $this->addSql('CREATE INDEX IDX_A352F0A0BA8B0AA4 ON CARS (BRAND_ID)');
        $this->addSql('ALTER TABLE CARS ADD CONSTRAINT FK_A352F0A0BA8B0AA4 FOREIGN KEY (BRAND_ID) REFERENCES BRANDS (ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CARS DROP FOREIGN KEY FK_A352F0A0BA8B0AA4');
        $this->addSql('DROP INDEX FK_A352F0A0BA2344A9 ON BRANDS');
        $this->addSql('ALTER TABLE CARS DROP BRAND_ID');
        $this->addSql('DROP INDEX IDX_A352F0A0BA8B0AA4 ON CARS');
    }
}
