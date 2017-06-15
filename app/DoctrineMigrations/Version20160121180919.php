<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160121180919 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE SERVICE_TYPES_SERVICE_REASONS');
        $this->addSql('ALTER TABLE SERVICE_TYPES ADD SERVICE_REASON_ID INT DEFAULT NULL');
        $this->addSql('ALTER TABLE SERVICE_TYPES ADD CONSTRAINT FK_D1A263042C4B67CC FOREIGN KEY (SERVICE_REASON_ID) REFERENCES SERVICE_REASONS (ID)');
        $this->addSql('CREATE INDEX IDX_D1A263042C4B67CC ON SERVICE_TYPES (SERVICE_REASON_ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE SERVICE_TYPES_SERVICE_REASONS (CAR_SERVICE_TYPE_ID INT NOT NULL, SERVICE_REASON_ID INT NOT NULL, INDEX IDX_663A5344C6DC1B82 (CAR_SERVICE_TYPE_ID), INDEX IDX_663A53442C4B67CC (SERVICE_REASON_ID), PRIMARY KEY(CAR_SERVICE_TYPE_ID, SERVICE_REASON_ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE SERVICE_TYPES_SERVICE_REASONS ADD CONSTRAINT FK_663A53442C4B67CC FOREIGN KEY (SERVICE_REASON_ID) REFERENCES SERVICE_REASONS (ID)');
        $this->addSql('ALTER TABLE SERVICE_TYPES_SERVICE_REASONS ADD CONSTRAINT FK_663A5344C6DC1B82 FOREIGN KEY (CAR_SERVICE_TYPE_ID) REFERENCES SERVICE_TYPES (ID)');
        $this->addSql('ALTER TABLE SERVICE_TYPES DROP FOREIGN KEY FK_D1A263042C4B67CC');
        $this->addSql('DROP INDEX IDX_D1A263042C4B67CC ON SERVICE_TYPES');
        $this->addSql('ALTER TABLE SERVICE_TYPES DROP SERVICE_REASON_ID');
    }
}
