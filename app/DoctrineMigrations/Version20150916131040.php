<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150916131040 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CAR_OWNER_REQUESTS DROP FOREIGN KEY FK_AD1FE89BA4494109');
        $this->addSql('DROP INDEX IDX_AD1FE89BA4494109 ON CAR_OWNER_REQUESTS');
        $this->addSql('ALTER TABLE CAR_OWNER_REQUESTS CHANGE COMPANY_ID CAR_SERVICE_ID BIGINT NOT NULL');
        $this->addSql('ALTER TABLE CAR_OWNER_REQUESTS ADD CONSTRAINT FK_AD1FE89B8A7A94FD FOREIGN KEY (CAR_SERVICE_ID) REFERENCES CAR_SERVICES (ID)');
        $this->addSql('CREATE INDEX IDX_AD1FE89B8A7A94FD ON CAR_OWNER_REQUESTS (CAR_SERVICE_ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CAR_OWNER_REQUESTS DROP FOREIGN KEY FK_AD1FE89B8A7A94FD');
        $this->addSql('DROP INDEX IDX_AD1FE89B8A7A94FD ON CAR_OWNER_REQUESTS');
        $this->addSql('ALTER TABLE CAR_OWNER_REQUESTS CHANGE CAR_SERVICE_ID COMPANY_ID BIGINT NOT NULL');
        $this->addSql('ALTER TABLE CAR_OWNER_REQUESTS ADD CONSTRAINT FK_AD1FE89BA4494109 FOREIGN KEY (COMPANY_ID) REFERENCES COMPANIES (ID)');
        $this->addSql('CREATE INDEX IDX_AD1FE89BA4494109 ON CAR_OWNER_REQUESTS (COMPANY_ID)');
    }
}
