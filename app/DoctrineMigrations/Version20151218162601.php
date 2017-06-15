<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151218162601 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE CAR_SERVICE_SERVICE_GROUPS (CAR_SERVICE_ID BIGINT NOT NULL, SERVICE_GROUP_ID INT NOT NULL, INDEX IDX_7376C16F8A7A94FD (CAR_SERVICE_ID), INDEX IDX_7376C16FC94C79E7 (SERVICE_GROUP_ID), PRIMARY KEY(CAR_SERVICE_ID, SERVICE_GROUP_ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE CAR_SERVICE_SERVICE_GROUPS ADD CONSTRAINT FK_7376C16F8A7A94FD FOREIGN KEY (CAR_SERVICE_ID) REFERENCES CAR_SERVICES (ID)');
        $this->addSql('ALTER TABLE CAR_SERVICE_SERVICE_GROUPS ADD CONSTRAINT FK_7376C16FC94C79E7 FOREIGN KEY (SERVICE_GROUP_ID) REFERENCES SERVICE_GROUPS (ID)');
        $this->addSql('DROP TABLE CAR_SERVICE_SERVICES');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE CAR_SERVICE_SERVICES (CAR_SERVICE_ID BIGINT NOT NULL, SERVICE_ID BIGINT NOT NULL, INDEX IDX_82DDDEFA8A7A94FD (CAR_SERVICE_ID), INDEX IDX_82DDDEFADE8EF239 (SERVICE_ID), PRIMARY KEY(CAR_SERVICE_ID, SERVICE_ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE CAR_SERVICE_SERVICES ADD CONSTRAINT FK_82DDDEFA8A7A94FD FOREIGN KEY (CAR_SERVICE_ID) REFERENCES CAR_SERVICES (ID)');
        $this->addSql('ALTER TABLE CAR_SERVICE_SERVICES ADD CONSTRAINT FK_82DDDEFADE8EF239 FOREIGN KEY (SERVICE_ID) REFERENCES SERVICES (ID)');
        $this->addSql('DROP TABLE CAR_SERVICE_SERVICE_GROUPS');
    }
}
