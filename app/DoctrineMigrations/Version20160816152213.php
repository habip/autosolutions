<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160816152213 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE EMPLOYEES (ID INT AUTO_INCREMENT NOT NULL, FIRST_NAME VARCHAR(255) NOT NULL, LAST_NAME VARCHAR(255) NOT NULL, POSITION VARCHAR(255) NOT NULL, USER_ID BIGINT DEFAULT NULL, CAR_SERVICE_ID BIGINT NOT NULL, UNIQUE INDEX UNIQ_FE40DDEFA0666B6F (USER_ID), INDEX IDX_FE40DDEF8A7A94FD (CAR_SERVICE_ID), PRIMARY KEY(ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE EMPLOYEES ADD CONSTRAINT FK_FE40DDEFA0666B6F FOREIGN KEY (USER_ID) REFERENCES USERS (ID)');
        $this->addSql('ALTER TABLE EMPLOYEES ADD CONSTRAINT FK_FE40DDEF8A7A94FD FOREIGN KEY (CAR_SERVICE_ID) REFERENCES CAR_SERVICES (ID)');
        $this->addSql('ALTER TABLE CAR_SERVICES CHANGE IS_TUS_DAY_OFF IS_TUE_DAY_OFF TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE USERS CHANGE TYPE TYPE enum(\'company\', \'car_owner\', \'agent\', \'employee\') NOT NULL DEFAULT \'car_owner\'');
        $this->addSql('ALTER TABLE CAR_SERVICE_SCHEDULE ADD TYPE VARCHAR(10) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE EMPLOYEES');
        $this->addSql('ALTER TABLE CAR_SERVICES CHANGE IS_TUE_DAY_OFF IS_TUS_DAY_OFF TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE CAR_SERVICE_SCHEDULE DROP TYPE');
        $this->addSql('ALTER TABLE USERS CHANGE TYPE TYPE enum(\'company\', \'car_owner\', \'agent\') DEFAULT \'car_owner\' NOT NULL COLLATE utf8_unicode_ci');
    }
}
