<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160218193016 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE REVIEWS (ID BIGINT AUTO_INCREMENT NOT NULL, RATING SMALLINT NOT NULL, MESSAGE LONGTEXT NOT NULL, CREATED_TIMESTAMP timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, RESPONSE LONGTEXT DEFAULT NULL, RESPONSE_TIMESTAMP timestamp NULL DEFAULT NULL, USER_ID BIGINT DEFAULT NULL, CAR_SERVICE_ID BIGINT DEFAULT NULL, INDEX IDX_56351515A0666B6F (USER_ID), INDEX IDX_563515158A7A94FD (CAR_SERVICE_ID), PRIMARY KEY(ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE REVIEWS ADD CONSTRAINT FK_56351515A0666B6F FOREIGN KEY (USER_ID) REFERENCES USERS (ID)');
        $this->addSql('ALTER TABLE REVIEWS ADD CONSTRAINT FK_563515158A7A94FD FOREIGN KEY (CAR_SERVICE_ID) REFERENCES CAR_SERVICES (ID)');
        $this->addSql('ALTER TABLE CAR_SERVICES ADD AVERAGE_RATING DOUBLE PRECISION DEFAULT NULL, ADD SUM_RATING BIGINT DEFAULT NULL, ADD RATING_5_COUNT INT NOT NULL, ADD RATING_4_COUNT INT NOT NULL, ADD RATING_3_COUNT INT NOT NULL, ADD RATING_2_COUNT INT NOT NULL, ADD RATING_1_COUNT INT NOT NULL, ADD REVIEW_COUNT INT NOT NULL');
        $this->addSql('ALTER TABLE CAR_OWNER_REQUESTS ADD REVIEW_ID BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE CAR_OWNER_REQUESTS ADD CONSTRAINT FK_AD1FE89B42A1CE94 FOREIGN KEY (REVIEW_ID) REFERENCES REVIEWS (ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CAR_OWNER_REQUESTS DROP FOREIGN KEY FK_AD1FE89B42A1CE94');
        $this->addSql('DROP TABLE REVIEWS');
        $this->addSql('ALTER TABLE CAR_OWNER_REQUESTS DROP REVIEW_ID');
        $this->addSql('ALTER TABLE CAR_SERVICES DROP AVERAGE_RATING, DROP SUM_RATING, DROP RATING_5_COUNT, DROP RATING_4_COUNT, DROP RATING_3_COUNT, DROP RATING_2_COUNT, DROP RATING_1_COUNT, DROP REVIEW_COUNT');
    }
}
