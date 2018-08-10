<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180810005912 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_purchase (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, product_id INT NOT NULL, purchased_at DATETIME NOT NULL, purchase_price DOUBLE PRECISION NOT NULL, INDEX IDX_819A353BA76ED395 (user_id), INDEX IDX_819A353B4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_purchase ADD CONSTRAINT FK_819A353BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_purchase ADD CONSTRAINT FK_819A353B4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product CHANGE details details VARCHAR(255) DEFAULT NULL, CHANGE upc upc VARCHAR(255) DEFAULT NULL, CHANGE image_url image_url VARCHAR(255) DEFAULT NULL, CHANGE released_at released_at DATETIME DEFAULT NULL, CHANGE customer_price customer_price DOUBLE PRECISION DEFAULT NULL, CHANGE vendor_price vendor_price DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE email email VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user_purchase');
        $this->addSql('ALTER TABLE product CHANGE details details VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE upc upc VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE image_url image_url VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE customer_price customer_price DOUBLE PRECISION DEFAULT \'NULL\', CHANGE vendor_price vendor_price DOUBLE PRECISION DEFAULT \'NULL\', CHANGE released_at released_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user CHANGE email email VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
    }
}
