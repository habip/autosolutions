<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160103011640 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE HIGHWAYS (ID INT AUTO_INCREMENT NOT NULL, NAME VARCHAR(150) NOT NULL, LOCALITY_ID INT NOT NULL, INDEX IDX_8A8E29783DCCEE9A (LOCALITY_ID), PRIMARY KEY(ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE METRO_STATIONS (ID INT AUTO_INCREMENT NOT NULL, NAME VARCHAR(150) NOT NULL, LOCALITY_ID INT NOT NULL, INDEX IDX_5E493D053DCCEE9A (LOCALITY_ID), PRIMARY KEY(ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE HIGHWAYS ADD CONSTRAINT FK_8A8E29783DCCEE9A FOREIGN KEY (LOCALITY_ID) REFERENCES LOCALITIES (ID)');
        $this->addSql('ALTER TABLE METRO_STATIONS ADD CONSTRAINT FK_5E493D053DCCEE9A FOREIGN KEY (LOCALITY_ID) REFERENCES LOCALITIES (ID)');
        $this->addSql('ALTER TABLE CAR_OWNERS ADD NICKNAME VARCHAR(255) NOT NULL, ADD GENDER enum(\'male\',\'female\') DEFAULT NULL, ADD BIRTHDAY DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE CAR_SERVICES ADD METRO_STATION_ID INT DEFAULT NULL, ADD HIGHWAY_ID INT DEFAULT NULL');
        $this->addSql('ALTER TABLE CAR_SERVICES ADD CONSTRAINT FK_EBC71FDE3DD67C43 FOREIGN KEY (METRO_STATION_ID) REFERENCES METRO_STATIONS (ID)');
        $this->addSql('ALTER TABLE CAR_SERVICES ADD CONSTRAINT FK_EBC71FDEBD9D7B42 FOREIGN KEY (HIGHWAY_ID) REFERENCES HIGHWAYS (ID)');
        $this->addSql('CREATE INDEX IDX_EBC71FDE3DD67C43 ON CAR_SERVICES (METRO_STATION_ID)');
        $this->addSql('CREATE INDEX IDX_EBC71FDEBD9D7B42 ON CAR_SERVICES (HIGHWAY_ID)');
    }

    public function postUp(Schema $schema)
    {
        $result = $this->connection->executeQuery('
                SELECT c.ID, u.EMAIL
                FROM CAR_OWNERS c join USERS u on c.USER_ID = u.ID
                ');

        while ($carOwner = $result->fetch()) {
            $this->connection->executeUpdate('
                    update CAR_OWNERS set NICKNAME = :nickname where ID = :id',
                    array(
                            'nickname' => $carOwner['EMAIL'],
                            'id' => $carOwner['ID'],
                    ));
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CAR_SERVICES DROP FOREIGN KEY FK_EBC71FDEBD9D7B42');
        $this->addSql('ALTER TABLE CAR_SERVICES DROP FOREIGN KEY FK_EBC71FDE3DD67C43');
        $this->addSql('DROP TABLE HIGHWAYS');
        $this->addSql('DROP TABLE METRO_STATIONS');
        $this->addSql('ALTER TABLE CAR_OWNERS DROP NICKNAME, DROP GENDER, DROP BIRTHDAY');
        $this->addSql('ALTER TABLE CAR_SERVICES DROP METRO_STATION_ID, DROP HIGHWAY_ID');
    }
}
