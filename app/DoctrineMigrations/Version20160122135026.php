<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160122135026 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CAR_SERVICES DROP FOREIGN KEY FK_EBC71FDE5AB6F15D');
        $this->addSql('DROP INDEX IDX_EBC71FDE5AB6F15D ON CAR_SERVICES');
        $this->addSql('ALTER TABLE CAR_SERVICES DROP ROAD_ZONE_IMAGE_ID');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CAR_SERVICES ADD ROAD_ZONE_IMAGE_ID BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE CAR_SERVICES ADD CONSTRAINT FK_EBC71FDE5AB6F15D FOREIGN KEY (ROAD_ZONE_IMAGE_ID) REFERENCES IMAGES (ID)');
        $this->addSql('CREATE INDEX IDX_EBC71FDE5AB6F15D ON CAR_SERVICES (ROAD_ZONE_IMAGE_ID)');
    }
}
