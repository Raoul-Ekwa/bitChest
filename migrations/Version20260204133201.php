<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260204133201 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE administrator (id INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE client (phone VARCHAR(20) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, is_active TINYINT NOT NULL, id INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE cryptocurrency (id INT AUTO_INCREMENT NOT NULL, symbol VARCHAR(10) NOT NULL, name VARCHAR(100) NOT NULL, current_price NUMERIC(18, 8) NOT NULL, image VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_CC62CFADECC836F9 (symbol), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE holding (id INT AUTO_INCREMENT NOT NULL, quantity NUMERIC(18, 8) NOT NULL, average_purchase_price NUMERIC(18, 8) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, wallet_id INT NOT NULL, cryptocurrency_id INT NOT NULL, INDEX IDX_5BBFD816712520F3 (wallet_id), INDEX IDX_5BBFD816583FC03A (cryptocurrency_id), UNIQUE INDEX unique_wallet_crypto (wallet_id, cryptocurrency_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE quote (id INT AUTO_INCREMENT NOT NULL, price NUMERIC(18, 8) NOT NULL, created_at DATETIME NOT NULL, cryptocurrency_id INT NOT NULL, INDEX IDX_6B71CBF4583FC03A (cryptocurrency_id), INDEX idx_quote_crypto_date (cryptocurrency_id, created_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, quantity NUMERIC(18, 8) NOT NULL, price_at_transaction NUMERIC(18, 8) NOT NULL, total_amount NUMERIC(15, 2) NOT NULL, created_at DATETIME NOT NULL, wallet_id INT NOT NULL, cryptocurrency_id INT NOT NULL, INDEX IDX_723705D1712520F3 (wallet_id), INDEX IDX_723705D1583FC03A (cryptocurrency_id), INDEX idx_transaction_wallet_date (wallet_id, created_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, type VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE wallet (id INT AUTO_INCREMENT NOT NULL, balance NUMERIC(15, 2) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, client_id INT NOT NULL, UNIQUE INDEX UNIQ_7C68921F19EB6921 (client_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE administrator ADD CONSTRAINT FK_58DF0651BF396750 FOREIGN KEY (id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455BF396750 FOREIGN KEY (id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE holding ADD CONSTRAINT FK_5BBFD816712520F3 FOREIGN KEY (wallet_id) REFERENCES wallet (id)');
        $this->addSql('ALTER TABLE holding ADD CONSTRAINT FK_5BBFD816583FC03A FOREIGN KEY (cryptocurrency_id) REFERENCES cryptocurrency (id)');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF4583FC03A FOREIGN KEY (cryptocurrency_id) REFERENCES cryptocurrency (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1712520F3 FOREIGN KEY (wallet_id) REFERENCES wallet (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1583FC03A FOREIGN KEY (cryptocurrency_id) REFERENCES cryptocurrency (id)');
        $this->addSql('ALTER TABLE wallet ADD CONSTRAINT FK_7C68921F19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE administrator DROP FOREIGN KEY FK_58DF0651BF396750');
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455BF396750');
        $this->addSql('ALTER TABLE holding DROP FOREIGN KEY FK_5BBFD816712520F3');
        $this->addSql('ALTER TABLE holding DROP FOREIGN KEY FK_5BBFD816583FC03A');
        $this->addSql('ALTER TABLE quote DROP FOREIGN KEY FK_6B71CBF4583FC03A');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1712520F3');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1583FC03A');
        $this->addSql('ALTER TABLE wallet DROP FOREIGN KEY FK_7C68921F19EB6921');
        $this->addSql('DROP TABLE administrator');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE cryptocurrency');
        $this->addSql('DROP TABLE holding');
        $this->addSql('DROP TABLE quote');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE wallet');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
