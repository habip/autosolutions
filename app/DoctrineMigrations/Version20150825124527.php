<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150825124527 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX company_email_idx ON COMPANIES');
        $this->addSql('ALTER TABLE USERS ADD USERNAME VARCHAR(255) NOT NULL, ADD PASSWORD VARCHAR(255) NOT NULL, ADD SALT VARCHAR(255) NOT NULL, ADD EMAIL VARCHAR(255) NOT NULL, ADD REGISTRATION_DATE DATETIME NOT NULL, ADD IS_ACTIVE TINYINT(1) NOT NULL, ADD CONFIRMATION_TOKEN VARCHAR(255) DEFAULT NULL, ADD BLOCKED TINYINT(1) DEFAULT NULL, ADD IS_ONLINE TINYINT(1) NOT NULL, ADD LAST_VISIT timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE TYPE TYPE enum(\'company\', \'car_owner\') NOT NULL DEFAULT \'car_owner\'');
        $this->addSql('CREATE UNIQUE INDEX user_email_idx ON USERS (EMAIL)');
    }

    public function postUp(Schema $schema)
    {
        $result = $this->connection->executeQuery('
                SELECT ID, USERNAME, PASSWORD, SALT, EMAIL, REGISTRATION_DATE, IS_ACTIVE, CONFIRMATION_TOKEN, BLOCKED, IS_ONLINE, LAST_VISIT
                FROM COMPANIES
                ');

        while ($company = $result->fetch()) {
            $this->connection->executeUpdate('
                    INSERT INTO USERS (TYPE, USERNAME, PASSWORD, SALT, EMAIL, REGISTRATION_DATE, IS_ACTIVE, CONFIRMATION_TOKEN, BLOCKED, IS_ONLINE, LAST_VISIT)
                    VALUES (\'company\', :username, :pass, :salt, :email, :reg_date, :is_active, :conf_token, :blocked, :is_online, :last_visit)',
                    array(
                            'username' => $company['USERNAME'],
                            'pass' => $company['PASSWORD'],
                            'salt' => $company['SALT'],
                            'email' => $company['EMAIL'],
                            'reg_date' => $company['REGISTRATION_DATE'],
                            'is_active' => $company['IS_ACTIVE'],
                            'conf_token' => $company['CONFIRMATION_TOKEN'],
                            'blocked' => $company['BLOCKED'],
                            'is_online' => $company['IS_ONLINE'],
                            'last_visit' => $company['LAST_VISIT'],
                    ));

            $this->connection->executeUpdate('UPDATE COMPANIES SET USER_ID = :user WHERE ID = :id',
                    array(
                            'user' => $this->connection->lastInsertId(),
                            'id' => $company['ID']
                    ));
        }

        $this->connection->exec('ALTER TABLE COMPANIES DROP USERNAME, DROP PASSWORD, DROP SALT, DROP EMAIL, DROP REGISTRATION_DATE, DROP IS_ACTIVE, DROP CONFIRMATION_TOKEN, DROP BLOCKED, DROP IS_ONLINE, DROP LAST_VISIT');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE COMPANIES ADD USERNAME VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD PASSWORD VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD SALT VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD EMAIL VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD REGISTRATION_DATE DATETIME NOT NULL, ADD IS_ACTIVE TINYINT(1) NOT NULL, ADD CONFIRMATION_TOKEN VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD BLOCKED TINYINT(1) DEFAULT NULL, ADD IS_ONLINE TINYINT(1) NOT NULL, ADD LAST_VISIT timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX company_email_idx ON COMPANIES (EMAIL)');
        $this->addSql('DROP INDEX user_email_idx ON USERS');
    }

    public function postDown(Schema $schema)
    {
        $result = $this->connection->executeQuery('
                SELECT ID, USERNAME, PASSWORD, SALT, EMAIL, REGISTRATION_DATE, IS_ACTIVE, CONFIRMATION_TOKEN, BLOCKED, IS_ONLINE, LAST_VISIT
                FROM USERS
                ');

        while ($user = $result->fetch()) {
            $this->connection->executeUpdate('
                    UPDATE COMPANIES
                    SET
                    USERNAME = :username,
                    PASSWORD = :pass,
                    SALT = :salt,
                    EMAIL = :email,
                    REGISTRATION_DATE = :reg_date,
                    IS_ACTIVE = :is_active,
                    CONFIRMATION_TOKEN = :conf_token,
                    BLOCKED = :blocked,
                    IS_ONLINE = :is_online,
                    LAST_VISIT = :last_visit
                    WHERE USER_ID = :id',
                    array(
                            'username' => $user['USERNAME'],
                            'pass' => $user['USERNAME'],
                            'salt' => $user['SALT'],
                            'email' => $user['EMAIL'],
                            'reg_date' => $user['REGISTRATION_DATE'],
                            'is_active' => $user['IS_ACTIVE'],
                            'conf_token' => $user['CONFIRMATION_TOKEN'],
                            'blocked' => $user['BLOCKED'],
                            'is_online' => $user['IS_ONLINE'],
                            'last_visit' => $user['LAST_VISIT'],
                            'id' => $user['ID']
                    ));
        }

        $this->connection->exec('ALTER TABLE USERS DROP USERNAME, DROP PASSWORD, DROP SALT, DROP EMAIL, DROP REGISTRATION_DATE, DROP IS_ACTIVE, DROP CONFIRMATION_TOKEN, DROP BLOCKED, DROP IS_ONLINE, DROP LAST_VISIT, CHANGE TYPE TYPE VARCHAR(255) DEFAULT \'car_owner\' NOT NULL COLLATE utf8_unicode_ci');
        $this->connection->executeUpdate('DELETE FROM USERS');
    }
}
