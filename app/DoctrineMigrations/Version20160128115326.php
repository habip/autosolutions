<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160128115326 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CAR_SERVICES ADD MON_START TIME DEFAULT NULL, ADD MON_END TIME DEFAULT NULL, ADD IS_MON_24_HRS TINYINT(1) NOT NULL, ADD IS_MON_DAY_OFF TINYINT(1) NOT NULL, ADD TUE_START TIME DEFAULT NULL, ADD TUE_END TIME DEFAULT NULL, ADD IS_TUE_24_HRS TINYINT(1) NOT NULL, ADD IS_TUS_DAY_OFF TINYINT(1) NOT NULL, ADD WED_START TIME DEFAULT NULL, ADD WED_END TIME DEFAULT NULL, ADD IS_WED_24_HRS TINYINT(1) NOT NULL, ADD IS_WED_DAY_OFF TINYINT(1) NOT NULL, ADD THU_START TIME DEFAULT NULL, ADD THU_END TIME DEFAULT NULL, ADD IS_THU_24_HRS TINYINT(1) NOT NULL, ADD IS_THU_DAY_OFF TINYINT(1) NOT NULL, ADD FRI_START TIME DEFAULT NULL, ADD FRI_END TIME DEFAULT NULL, ADD IS_FRI_24_HRS TINYINT(1) NOT NULL, ADD IS_FRI_DAY_OFF TINYINT(1) NOT NULL, ADD SAT_START TIME DEFAULT NULL, ADD SAT_END TIME DEFAULT NULL, ADD IS_SAT_24_HRS TINYINT(1) NOT NULL, ADD IS_SAT_DAY_OFF TINYINT(1) NOT NULL, ADD SUN_START TIME DEFAULT NULL, ADD SUN_END TIME DEFAULT NULL, ADD IS_SUN_24_HRS TINYINT(1) NOT NULL, ADD IS_SUN_DAY_OFF TINYINT(1) NOT NULL, ADD IS_BLOCKED TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE CAR_MODELS ADD VEHICLE_TYPE VARCHAR(150) DEFAULT NULL, ADD VEHICLE_CLASS VARCHAR(2) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CAR_SERVICES DROP MON_START, DROP MON_END, DROP IS_MON_24_HRS, DROP IS_MON_DAY_OFF, DROP TUE_START, DROP TUE_END, DROP IS_TUE_24_HRS, DROP IS_TUS_DAY_OFF, DROP WED_START, DROP WED_END, DROP IS_WED_24_HRS, DROP IS_WED_DAY_OFF, DROP THU_START, DROP THU_END, DROP IS_THU_24_HRS, DROP IS_THU_DAY_OFF, DROP FRI_START, DROP FRI_END, DROP IS_FRI_24_HRS, DROP IS_FRI_DAY_OFF, DROP SAT_START, DROP SAT_END, DROP IS_SAT_24_HRS, DROP IS_SAT_DAY_OFF, DROP SUN_START, DROP SUN_END, DROP IS_SUN_24_HRS, DROP IS_SUN_DAY_OFF, DROP IS_BLOCKED');
        $this->addSql('ALTER TABLE CAR_MODELS DROP VEHICLE_TYPE, DROP VEHICLE_CLASS');
    }
}
