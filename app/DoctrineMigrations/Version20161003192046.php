<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161003192046 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE CAR_SERVICE_SERVICES (CAR_SERVICE_ID BIGINT NOT NULL, SERVICE_ID BIGINT NOT NULL, INDEX IDX_82DDDEFA8A7A94FD (CAR_SERVICE_ID), INDEX IDX_82DDDEFADE8EF239 (SERVICE_ID), PRIMARY KEY(CAR_SERVICE_ID, SERVICE_ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE CAR_SERVICE_SERVICES ADD CONSTRAINT FK_82DDDEFA8A7A94FD FOREIGN KEY (CAR_SERVICE_ID) REFERENCES CAR_SERVICES (ID)');
        $this->addSql('ALTER TABLE CAR_SERVICE_SERVICES ADD CONSTRAINT FK_82DDDEFADE8EF239 FOREIGN KEY (SERVICE_ID) REFERENCES SERVICES (ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE CAR_SERVICE_SERVICES');
    }
}
