<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160929173341 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE COMPANY_SERVICES (ID BIGINT AUTO_INCREMENT NOT NULL, NAME VARCHAR(255) NOT NULL, COST NUMERIC(10, 2) DEFAULT NULL, DURATION INT DEFAULT NULL, CREATED_TIMESTAMP timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, SERVICE_ID BIGINT DEFAULT NULL, COMPANY_ID BIGINT DEFAULT NULL, INDEX IDX_9D864979DE8EF239 (SERVICE_ID), INDEX IDX_9D864979A4494109 (COMPANY_ID), PRIMARY KEY(ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE COMPANY_SERVICES ADD CONSTRAINT FK_9D864979DE8EF239 FOREIGN KEY (SERVICE_ID) REFERENCES SERVICES (ID)');
        $this->addSql('ALTER TABLE COMPANY_SERVICES ADD CONSTRAINT FK_9D864979A4494109 FOREIGN KEY (COMPANY_ID) REFERENCES COMPANIES (ID)');
        $this->addSql('ALTER TABLE SERVICE_COSTS DROP FOREIGN KEY FK_278FBD9CA4494109');
        $this->addSql('ALTER TABLE SERVICE_COSTS DROP FOREIGN KEY FK_278FBD9CDE8EF239');
        $this->addSql('DROP INDEX IDX_278FBD9CDE8EF239 ON SERVICE_COSTS');
        $this->addSql('DROP INDEX IDX_278FBD9CA4494109 ON SERVICE_COSTS');
        $this->addSql('ALTER TABLE SERVICE_COSTS ADD COMPANY_SERVICE_ID BIGINT DEFAULT NULL, DROP NAME, DROP SERVICE_ID, DROP COMPANY_ID');
        $this->addSql('ALTER TABLE SERVICE_COSTS ADD CONSTRAINT FK_278FBD9CE9343C53 FOREIGN KEY (COMPANY_SERVICE_ID) REFERENCES COMPANY_SERVICES (ID)');
        $this->addSql('CREATE INDEX IDX_278FBD9CE9343C53 ON SERVICE_COSTS (COMPANY_SERVICE_ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE SERVICE_COSTS DROP FOREIGN KEY FK_278FBD9CE9343C53');
        $this->addSql('DROP TABLE COMPANY_SERVICES');
        $this->addSql('ALTER TABLE SERVICE_COSTS ADD NAME VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD COMPANY_ID BIGINT DEFAULT NULL, DROP COMPANY_SERVICE_ID ADD SERVICE_ID BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE SERVICE_COSTS ADD CONSTRAINT FK_278FBD9CA4494109 FOREIGN KEY (COMPANY_ID) REFERENCES COMPANIES (ID)');
        $this->addSql('ALTER TABLE SERVICE_COSTS ADD CONSTRAINT FK_278FBD9CDE8EF239 FOREIGN KEY (SERVICE_ID) REFERENCES SERVICES (ID)');
        $this->addSql('CREATE INDEX IDX_278FBD9CDE8EF239 ON SERVICE_COSTS (SERVICE_ID)');
        $this->addSql('CREATE INDEX IDX_278FBD9CA4494109 ON SERVICE_COSTS (COMPANY_ID)');
    }
}
