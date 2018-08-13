<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180811143948 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product ADD customer_discount_price DOUBLE PRECISION DEFAULT NULL, CHANGE details details VARCHAR(255) DEFAULT NULL, CHANGE upc upc VARCHAR(255) DEFAULT NULL, CHANGE image_url image_url VARCHAR(255) DEFAULT NULL, CHANGE released_at released_at DATETIME DEFAULT NULL, CHANGE customer_price customer_price DOUBLE PRECISION DEFAULT NULL, CHANGE vendor_price vendor_price DOUBLE PRECISION DEFAULT NULL, CHANGE deadline_at deadline_at DATETIME DEFAULT NULL, CHANGE customer_discount customer_discount DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE email email VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product DROP customer_discount_price, CHANGE details details VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE upc upc VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE image_url image_url VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE customer_price customer_price DOUBLE PRECISION DEFAULT \'NULL\', CHANGE vendor_price vendor_price DOUBLE PRECISION DEFAULT \'NULL\', CHANGE released_at released_at DATETIME DEFAULT \'NULL\', CHANGE deadline_at deadline_at DATETIME DEFAULT \'NULL\', CHANGE customer_discount customer_discount DOUBLE PRECISION DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user CHANGE email email VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
    }
}
