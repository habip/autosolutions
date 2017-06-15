<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160803184434 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE CAR_SERVICE_POST (ID INT AUTO_INCREMENT NOT NULL, NAME VARCHAR(100) NOT NULL, DESCRIPTION VARCHAR(255) NOT NULL, CAR_SERVICE_ID BIGINT NOT NULL, INDEX IDX_CB7D3CA68A7A94FD (CAR_SERVICE_ID), PRIMARY KEY(ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE CAR_SERVICE_POST ADD CONSTRAINT FK_CB7D3CA68A7A94FD FOREIGN KEY (CAR_SERVICE_ID) REFERENCES CAR_SERVICES (ID)');
        $this->addSql('ALTER TABLE CAR_OWNER_REQUESTS ADD ENTRY_TIME DATETIME DEFAULT NULL, ADD EXIT_TIME DATETIME DEFAULT NULL, ADD CAR_SERVICE_POST_ID INT DEFAULT NULL');
        $this->addSql('ALTER TABLE CAR_OWNER_REQUESTS ADD CONSTRAINT FK_AD1FE89B4819943D FOREIGN KEY (CAR_SERVICE_POST_ID) REFERENCES CAR_SERVICE_POST (ID)');
        $this->addSql('CREATE INDEX IDX_AD1FE89B4819943D ON CAR_OWNER_REQUESTS (CAR_SERVICE_POST_ID)');
        $this->addSql('CREATE TABLE CAR_SERVICE_SCHEDULE (ID BIGINT AUTO_INCREMENT NOT NULL, START_TIME DATETIME NOT NULL, END_TIME DATETIME NOT NULL, CREATED_TIMESTAMP DATETIME NOT NULL, CAR_SERVICE_ID BIGINT NOT NULL, POST_ID INT NOT NULL, REQUEST_ID BIGINT DEFAULT NULL, INDEX IDX_ABD72E688A7A94FD (CAR_SERVICE_ID), INDEX IDX_ABD72E684C81BBD6 (POST_ID), INDEX IDX_ABD72E6871ACE37A (REQUEST_ID), PRIMARY KEY(ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE CAR_SERVICE_SCHEDULE ADD CONSTRAINT FK_ABD72E688A7A94FD FOREIGN KEY (CAR_SERVICE_ID) REFERENCES CAR_SERVICES (ID)');
        $this->addSql('ALTER TABLE CAR_SERVICE_SCHEDULE ADD CONSTRAINT FK_ABD72E684C81BBD6 FOREIGN KEY (POST_ID) REFERENCES CAR_SERVICE_POST (ID)');
        $this->addSql('ALTER TABLE CAR_SERVICE_SCHEDULE ADD CONSTRAINT FK_ABD72E6871ACE37A FOREIGN KEY (REQUEST_ID) REFERENCES CAR_OWNER_REQUESTS (ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CAR_OWNER_REQUESTS DROP FOREIGN KEY FK_AD1FE89B4819943D');
        $this->addSql('DROP TABLE CAR_SERVICE_POST');
        $this->addSql('ALTER TABLE CAR_OWNER_REQUESTS DROP ENTRY_TIME, DROP EXIT_TIME, DROP CAR_SERVICE_POST_ID');
        $this->addSql('DROP TABLE CAR_SERVICE_SCHEDULE');
    }
}
