<?php
/*
 * Copyright 2016 CampaignChain, Inc. <info@campaignchain.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160621000003 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE campaignchain_security_authentication_client_oauth_application (id INT AUTO_INCREMENT NOT NULL, resourceOwner VARCHAR(255) NOT NULL, `key` VARCHAR(255) NOT NULL, secret VARCHAR(255) NOT NULL, createdDate DATETIME NOT NULL, modifiedDate DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE campaignchain_security_authentication_client_oauth_token (id INT AUTO_INCREMENT NOT NULL, application_id INT DEFAULT NULL, location_id INT DEFAULT NULL, accessToken VARCHAR(255) NOT NULL, refreshToken VARCHAR(255) DEFAULT NULL, tokenSecret VARCHAR(255) DEFAULT NULL, expiresIn VARCHAR(255) DEFAULT NULL, expiresAt VARCHAR(255) DEFAULT NULL, scope LONGTEXT DEFAULT NULL, endpoint VARCHAR(255) DEFAULT NULL, createdDate DATETIME NOT NULL, modifiedDate DATETIME DEFAULT NULL, INDEX IDX_8A5B77663E030ACD (application_id), UNIQUE INDEX UNIQ_8A5B776664D218E (location_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE campaignchain_security_authentication_client_oauth_token ADD CONSTRAINT FK_8A5B77663E030ACD FOREIGN KEY (application_id) REFERENCES campaignchain_security_authentication_client_oauth_application (id)');
        $this->addSql('ALTER TABLE campaignchain_security_authentication_client_oauth_token ADD CONSTRAINT FK_8A5B776664D218E FOREIGN KEY (location_id) REFERENCES campaignchain_location (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE campaignchain_security_authentication_client_oauth_token DROP FOREIGN KEY FK_8A5B77663E030ACD');
        $this->addSql('DROP TABLE campaignchain_security_authentication_client_oauth_application');
        $this->addSql('DROP TABLE campaignchain_security_authentication_client_oauth_token');
    }
}
