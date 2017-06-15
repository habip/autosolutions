<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160324132758 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CAR_SERVICES ADD AVERAGE_DESCRIPTION_RATING DOUBLE PRECISION DEFAULT NULL, ADD SUM_DESCRIPTION_RATING BIGINT DEFAULT NULL, ADD AVERAGE_COMMUNICATION_RATING DOUBLE PRECISION DEFAULT NULL, ADD SUM_COMMUNICATION_RATING BIGINT DEFAULT NULL, ADD AVERAGE_PRICE_RATING DOUBLE PRECISION DEFAULT NULL, ADD SUM_PRICE_RATING BIGINT DEFAULT NULL, ADD DETAILED_REVIEW_COUNT INT NOT NULL');
        $this->addSql('ALTER TABLE REVIEWS ADD DESCRIPTION_RATING SMALLINT DEFAULT NULL, ADD COMMUNICATION_RATING SMALLINT DEFAULT NULL, ADD PRICE_RATING SMALLINT DEFAULT NULL, CHANGE CREATED_TIMESTAMP CREATED_TIMESTAMP timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CAR_SERVICES DROP AVERAGE_DESCRIPTION_RATING, DROP SUM_DESCRIPTION_RATING, DROP AVERAGE_COMMUNICATION_RATING, DROP SUM_COMMUNICATION_RATING, DROP AVERAGE_PRICE_RATING, DROP SUM_PRICE_RATING, DROP DETAILED_REVIEW_COUNT');
        $this->addSql('ALTER TABLE REVIEWS DROP DESCRIPTION_RATING, DROP COMMUNICATION_RATING, DROP PRICE_RATING, CHANGE CREATED_TIMESTAMP CREATED_TIMESTAMP DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }
}
