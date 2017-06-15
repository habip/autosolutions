<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151203133811 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE SERVICE_REASONS (ID INT AUTO_INCREMENT NOT NULL, NAME VARCHAR(255) NOT NULL, PRIMARY KEY(ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE SERVICE_GROUPS ADD REASON_ID INT DEFAULT NULL');
        $this->addSql('ALTER TABLE SERVICE_GROUPS ADD CONSTRAINT FK_EA3F7FE725344D9D FOREIGN KEY (REASON_ID) REFERENCES SERVICE_REASONS (ID)');
        $this->addSql('CREATE INDEX IDX_EA3F7FE725344D9D ON SERVICE_GROUPS (REASON_ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE SERVICE_GROUPS DROP FOREIGN KEY FK_EA3F7FE725344D9D');
        $this->addSql('DROP TABLE SERVICE_REASONS');
        $this->addSql('ALTER TABLE SERVICE_GROUPS DROP REASON_ID');
    }
}
