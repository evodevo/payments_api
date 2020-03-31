<?php

declare(strict_types=1);

namespace PaymentsAPI\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200330213008 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE transactions (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, details VARCHAR(512) DEFAULT NULL, confirmation_code INT NOT NULL, status ENUM(\'created\', \'confirmed\', \'completed\', \'failed\') COMMENT \'(DC2Type:TransactionStatus)\' DEFAULT \'created\' NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, recipient_account VARCHAR(255) NOT NULL, recipient_name VARCHAR(255) NOT NULL, amount VARCHAR(255) NOT NULL, currency CHAR(3) NOT NULL, fee_amount VARCHAR(255) NOT NULL, fee_currency CHAR(3) NOT NULL, INDEX user_id_index (user_id), INDEX user_id_currency_index (user_id, currency), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE transactions');
    }
}
