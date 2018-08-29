<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180829012301 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE company CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE genre CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE product CHANGE details details VARCHAR(255) DEFAULT NULL, CHANGE upc upc VARCHAR(255) DEFAULT NULL, CHANGE image_url image_url VARCHAR(255) DEFAULT NULL, CHANGE vendor_price vendor_price NUMERIC(7, 2) DEFAULT NULL, CHANGE customer_price customer_price NUMERIC(7, 2) DEFAULT NULL, CHANGE customer_discount customer_discount INT DEFAULT NULL, CHANGE customer_discount_price customer_discount_price NUMERIC(7, 2) DEFAULT NULL, CHANGE pre_order_deadline_at pre_order_deadline_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', CHANGE released_at released_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\'');
        $this->addSql('ALTER TABLE product_type CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', CHANGE username username VARCHAR(255) DEFAULT NULL, CHANGE password password VARCHAR(255) DEFAULT NULL, CHANGE salt salt VARCHAR(32) DEFAULT NULL, CHANGE registration_verification_token registration_verification_token VARCHAR(255) DEFAULT NULL, CHANGE registration_verification_token_expires_at registration_verification_token_expires_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', CHANGE registration_verified_at registration_verified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\'');
        $this->addSql('ALTER TABLE user_purchase ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE company CHANGE description description LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE genre CHANGE description description LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE product CHANGE details details VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE upc upc VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE image_url image_url VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE vendor_price vendor_price NUMERIC(7, 2) DEFAULT \'NULL\', CHANGE customer_price customer_price NUMERIC(7, 2) DEFAULT \'NULL\', CHANGE customer_discount customer_discount INT DEFAULT NULL, CHANGE customer_discount_price customer_discount_price NUMERIC(7, 2) DEFAULT \'NULL\', CHANGE pre_order_deadline_at pre_order_deadline_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime)\', CHANGE released_at released_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime)\'');
        $this->addSql('ALTER TABLE product_type CHANGE description description LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE user DROP created_at, DROP updated_at, CHANGE username username VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE password password VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE salt salt VARCHAR(32) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE registration_verification_token registration_verification_token VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE registration_verification_token_expires_at registration_verification_token_expires_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime)\', CHANGE registration_verified_at registration_verified_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime)\'');
        $this->addSql('ALTER TABLE user_purchase DROP created_at, DROP updated_at');
    }
}
