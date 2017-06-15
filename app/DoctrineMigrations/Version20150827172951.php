<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150827172951 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE CAR_OWNERS (ID BIGINT AUTO_INCREMENT NOT NULL, FIRST_NAME VARCHAR(255) NOT NULL, LAST_NAME VARCHAR(255) NOT NULL, PHONE VARCHAR(255) NOT NULL, LOCALITY_ID INT NOT NULL, USER_ID BIGINT DEFAULT NULL, INDEX IDX_9E385AAB3DCCEE9A (LOCALITY_ID), UNIQUE INDEX UNIQ_9E385AABA0666B6F (USER_ID), PRIMARY KEY(ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE CAR_OWNERS ADD CONSTRAINT FK_9E385AAB3DCCEE9A FOREIGN KEY (LOCALITY_ID) REFERENCES LOCALITIES (ID)');
        $this->addSql('ALTER TABLE CAR_OWNERS ADD CONSTRAINT FK_9E385AABA0666B6F FOREIGN KEY (USER_ID) REFERENCES USERS (ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE CAR_OWNERS');
    }
}
