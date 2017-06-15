<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161025092002 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CAR_OWNER_REQUESTS DROP INDEX FK_AD1FE89B42A1CE94, ADD UNIQUE INDEX UNIQ_AD1FE89B42A1CE94 (REVIEW_ID)');
        $this->addSql('ALTER TABLE CAR_SERVICE_SCHEDULE DROP INDEX IDX_ABD72E6871ACE37A, ADD UNIQUE INDEX UNIQ_ABD72E6871ACE37A (REQUEST_ID), DROP `TYPE`');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CAR_OWNER_REQUESTS DROP INDEX UNIQ_AD1FE89B42A1CE94, ADD INDEX FK_AD1FE89B42A1CE94 (REVIEW_ID)');
        $this->addSql('ALTER TABLE CAR_SERVICE_SCHEDULE DROP INDEX UNIQ_ABD72E6871ACE37A, ADD INDEX IDX_ABD72E6871ACE37A (REQUEST_ID), ADD `TYPE` varchar(10) NOT NULL');
    }

    public function preUp(Schema $schema)
    {
        $this->connection->executeQuery('delete from CAR_SERVICE_SCHEDULE where type <> \'request\'');
    }
}
