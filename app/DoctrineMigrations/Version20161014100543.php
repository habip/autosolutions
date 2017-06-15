<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161014100543 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CAR_SERVICES ADD IS_24_HRS TINYINT(1) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CAR_SERVICES DROP IS_24_HRS');
    }

    public function postUp(Schema $schema)
    {
        $stmt = $this->connection->executeQuery('select c.id
                from CAR_SERVICES c
                where c.IS_MON_24_HRS = 1
                    or c.IS_TUE_24_HRS = 1
                    or c.IS_WED_24_HRS = 1
                    or c.IS_THU_24_HRS = 1
                    or c.IS_FRI_24_HRS = 1
                    or c.IS_SAT_24_HRS = 1
                    or c.IS_SUN_24_HRS = 1');

        while (($id = $stmt->fetchColumn(0)) !== false) {
            $this->connection->executeUpdate('update CAR_SERVICES set IS_24_HRS = 1 where ID = :id', array('id' => $id));
        }

    }
}
