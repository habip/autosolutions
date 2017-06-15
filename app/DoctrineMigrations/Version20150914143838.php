<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150914143838 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE CARS (ID BIGINT AUTO_INCREMENT NOT NULL, NAME VARCHAR(255) NOT NULL, CAR_OWNER_ID BIGINT DEFAULT NULL, INDEX IDX_A352F0A0DE84D9AE (CAR_OWNER_ID), PRIMARY KEY(ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE CAR_BRANDS (CAR_BRAND_ID BIGINT NOT NULL, BRAND_ID BIGINT NOT NULL, INDEX IDX_A2E88C65E44D685F (CAR_BRAND_ID), INDEX IDX_A2E88C65BA8B0AA4 (BRAND_ID), PRIMARY KEY(CAR_BRAND_ID, BRAND_ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE CAR_IMAGES (CAR_IMAGE_ID BIGINT NOT NULL, IMAGE_ID BIGINT NOT NULL, INDEX IDX_3C55763B9D1D9D3A (CAR_IMAGE_ID), INDEX IDX_3C55763BC3DBFFC1 (IMAGE_ID), PRIMARY KEY(CAR_IMAGE_ID, IMAGE_ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE CARS ADD CONSTRAINT FK_A352F0A0DE84D9AE FOREIGN KEY (CAR_OWNER_ID) REFERENCES CAR_OWNERS (ID)');
        $this->addSql('ALTER TABLE CAR_BRANDS ADD CONSTRAINT FK_A2E88C65E44D685F FOREIGN KEY (CAR_BRAND_ID) REFERENCES CARS (ID)');
        $this->addSql('ALTER TABLE CAR_BRANDS ADD CONSTRAINT FK_A2E88C65BA8B0AA4 FOREIGN KEY (BRAND_ID) REFERENCES BRANDS (ID)');
        $this->addSql('ALTER TABLE CAR_IMAGES ADD CONSTRAINT FK_3C55763B9D1D9D3A FOREIGN KEY (CAR_IMAGE_ID) REFERENCES CARS (ID)');
        $this->addSql('ALTER TABLE CAR_IMAGES ADD CONSTRAINT FK_3C55763BC3DBFFC1 FOREIGN KEY (IMAGE_ID) REFERENCES IMAGES (ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CAR_BRANDS DROP FOREIGN KEY FK_A2E88C65E44D685F');
        $this->addSql('ALTER TABLE CAR_IMAGES DROP FOREIGN KEY FK_3C55763B9D1D9D3A');
        $this->addSql('DROP TABLE CARS');
        $this->addSql('DROP TABLE CAR_BRANDS');
        $this->addSql('DROP TABLE CAR_IMAGES');
    }
}
