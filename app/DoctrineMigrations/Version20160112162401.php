<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160112162401 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CAR_IMAGES DROP FOREIGN KEY FK_3C55763B9D1D9D3A');
        $this->addSql('DROP INDEX IDX_3C55763B9D1D9D3A ON CAR_IMAGES');
        $this->addSql('ALTER TABLE CAR_IMAGES DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE CAR_IMAGES CHANGE car_image_id CAR_ID BIGINT NOT NULL');
        $this->addSql('ALTER TABLE CAR_IMAGES ADD CONSTRAINT FK_3C55763BB7377F9 FOREIGN KEY (CAR_ID) REFERENCES CARS (ID)');
        $this->addSql('CREATE INDEX IDX_3C55763BB7377F9 ON CAR_IMAGES (CAR_ID)');
        $this->addSql('ALTER TABLE CAR_IMAGES ADD PRIMARY KEY (CAR_ID, IMAGE_ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CAR_IMAGES DROP FOREIGN KEY FK_3C55763BB7377F9');
        $this->addSql('DROP INDEX IDX_3C55763BB7377F9 ON CAR_IMAGES');
        $this->addSql('ALTER TABLE CAR_IMAGES DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE CAR_IMAGES CHANGE car_id CAR_IMAGE_ID BIGINT NOT NULL');
        $this->addSql('ALTER TABLE CAR_IMAGES ADD CONSTRAINT FK_3C55763B9D1D9D3A FOREIGN KEY (CAR_IMAGE_ID) REFERENCES CARS (ID)');
        $this->addSql('CREATE INDEX IDX_3C55763B9D1D9D3A ON CAR_IMAGES (CAR_IMAGE_ID)');
        $this->addSql('ALTER TABLE CAR_IMAGES ADD PRIMARY KEY (CAR_IMAGE_ID, IMAGE_ID)');
    }
}
