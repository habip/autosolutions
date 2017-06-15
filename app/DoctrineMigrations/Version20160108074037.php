<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160108074037 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CAR_OWNER_REQUESTS CHANGE STATUS STATUS enum(\'new\', \'assign\', \'reassign\', \'rejected\', \'canceled\', \'timeout\', \'done\') NOT NULL DEFAULT \'new\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CAR_OWNER_REQUESTS CHANGE STATUS STATUS enum(\'new\', \'assign\', \'reassign\', \'rejected\', \'canceled\', \'done\') NOT NULL DEFAULT \'new\'');
    }
}
