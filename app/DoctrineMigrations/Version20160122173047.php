<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160122173047 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CAR_SERVICES_PAYMENT_TYPES DROP FOREIGN KEY FK_5C75EF1320EE4559');
        $this->addSql('DROP INDEX IDX_5C75EF1320EE4559 ON CAR_SERVICES_PAYMENT_TYPES');
        $this->addSql('ALTER TABLE CAR_SERVICES_PAYMENT_TYPES DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE CAR_SERVICES_PAYMENT_TYPES CHANGE payments_type_id PAYMENT_TYPE_ID INT NOT NULL');
        $this->addSql('ALTER TABLE CAR_SERVICES_PAYMENT_TYPES ADD CONSTRAINT FK_5C75EF137ED3E26C FOREIGN KEY (PAYMENT_TYPE_ID) REFERENCES PAYMENT_TYPES (ID)');
        $this->addSql('CREATE INDEX IDX_5C75EF137ED3E26C ON CAR_SERVICES_PAYMENT_TYPES (PAYMENT_TYPE_ID)');
        $this->addSql('ALTER TABLE CAR_SERVICES_PAYMENT_TYPES ADD PRIMARY KEY (CAR_SERVICE_ID, PAYMENT_TYPE_ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CAR_SERVICES_PAYMENT_TYPES DROP FOREIGN KEY FK_5C75EF137ED3E26C');
        $this->addSql('DROP INDEX IDX_5C75EF137ED3E26C ON CAR_SERVICES_PAYMENT_TYPES');
        $this->addSql('ALTER TABLE CAR_SERVICES_PAYMENT_TYPES DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE CAR_SERVICES_PAYMENT_TYPES CHANGE payment_type_id PAYMENTS_TYPE_ID INT NOT NULL');
        $this->addSql('ALTER TABLE CAR_SERVICES_PAYMENT_TYPES ADD CONSTRAINT FK_5C75EF1320EE4559 FOREIGN KEY (PAYMENTS_TYPE_ID) REFERENCES PAYMENT_TYPES (ID)');
        $this->addSql('CREATE INDEX IDX_5C75EF1320EE4559 ON CAR_SERVICES_PAYMENT_TYPES (PAYMENTS_TYPE_ID)');
        $this->addSql('ALTER TABLE CAR_SERVICES_PAYMENT_TYPES ADD PRIMARY KEY (CAR_SERVICE_ID, PAYMENTS_TYPE_ID)');
    }
}
