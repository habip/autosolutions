<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150916123503 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE CAR_OWNER_REQUESTS (ID BIGINT AUTO_INCREMENT NOT NULL, NAME VARCHAR(255) DEFAULT NULL, START_TIME DATETIME DEFAULT NULL, END_TIME DATETIME DEFAULT NULL, PHONE VARCHAR(50) DEFAULT NULL, EMAIL VARCHAR(50) DEFAULT NULL, DESCRIPTION LONGTEXT DEFAULT NULL, DIALOG_ID BIGINT DEFAULT NULL, ADDED_TIMESTAMP timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, CAR_ID BIGINT NOT NULL, COMPANY_ID BIGINT NOT NULL, CAR_OWNER_ID BIGINT NOT NULL, INDEX IDX_AD1FE89BB7377F9 (CAR_ID), INDEX IDX_AD1FE89BA4494109 (COMPANY_ID), INDEX IDX_AD1FE89BDE84D9AE (CAR_OWNER_ID), PRIMARY KEY(ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE CAR_OWNER_SERVICES (CAR_OWNER_REQUEST_SERVICE_ID BIGINT NOT NULL, SERVICE_ID BIGINT NOT NULL, INDEX IDX_A5A8DFA3EFD6A13A (CAR_OWNER_REQUEST_SERVICE_ID), INDEX IDX_A5A8DFA3DE8EF239 (SERVICE_ID), PRIMARY KEY(CAR_OWNER_REQUEST_SERVICE_ID, SERVICE_ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE CAR_OWNER_REQUESTS ADD CONSTRAINT FK_AD1FE89BB7377F9 FOREIGN KEY (CAR_ID) REFERENCES CARS (ID)');
        $this->addSql('ALTER TABLE CAR_OWNER_REQUESTS ADD CONSTRAINT FK_AD1FE89BA4494109 FOREIGN KEY (COMPANY_ID) REFERENCES COMPANIES (ID)');
        $this->addSql('ALTER TABLE CAR_OWNER_REQUESTS ADD CONSTRAINT FK_AD1FE89BDE84D9AE FOREIGN KEY (CAR_OWNER_ID) REFERENCES CAR_OWNERS (ID)');
        $this->addSql('ALTER TABLE CAR_OWNER_SERVICES ADD CONSTRAINT FK_A5A8DFA3EFD6A13A FOREIGN KEY (CAR_OWNER_REQUEST_SERVICE_ID) REFERENCES CAR_OWNER_REQUESTS (ID)');
        $this->addSql('ALTER TABLE CAR_OWNER_SERVICES ADD CONSTRAINT FK_A5A8DFA3DE8EF239 FOREIGN KEY (SERVICE_ID) REFERENCES SERVICES (ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CAR_OWNER_SERVICES DROP FOREIGN KEY FK_A5A8DFA3EFD6A13A');
        $this->addSql('DROP TABLE CAR_OWNER_REQUESTS');
        $this->addSql('DROP TABLE CAR_OWNER_SERVICES');
    }
}
