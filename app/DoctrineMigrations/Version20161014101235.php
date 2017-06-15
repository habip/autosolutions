<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161014101235 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CAR_SERVICES DROP IS_MON_24_HRS, DROP IS_TUE_24_HRS, DROP IS_WED_24_HRS, DROP IS_THU_24_HRS, DROP IS_FRI_24_HRS, DROP IS_SAT_24_HRS, DROP IS_SUN_24_HRS');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CAR_SERVICES ADD IS_MON_24_HRS TINYINT(1) NOT NULL, ADD IS_TUE_24_HRS TINYINT(1) NOT NULL, ADD IS_WED_24_HRS TINYINT(1) NOT NULL, ADD IS_THU_24_HRS TINYINT(1) NOT NULL, ADD IS_FRI_24_HRS TINYINT(1) NOT NULL, ADD IS_SAT_24_HRS TINYINT(1) NOT NULL, ADD IS_SUN_24_HRS TINYINT(1) NOT NULL');
    }
}
