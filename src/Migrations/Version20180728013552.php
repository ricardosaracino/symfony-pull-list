<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180728013552 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product CHANGE details details VARCHAR(255) DEFAULT NULL, CHANGE upc upc VARCHAR(255) DEFAULT NULL, CHANGE customer_cost customer_cost DOUBLE PRECISION DEFAULT NULL, CHANGE vendor_cost vendor_cost DOUBLE PRECISION DEFAULT NULL, CHANGE released_at released_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE password password VARCHAR(255) NOT NULL, CHANGE email email VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product CHANGE details details VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE upc upc VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE customer_cost customer_cost DOUBLE PRECISION DEFAULT \'NULL\', CHANGE vendor_cost vendor_cost DOUBLE PRECISION DEFAULT \'NULL\', CHANGE released_at released_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677 ON user');
        $this->addSql('ALTER TABLE user CHANGE password password VARCHAR(64) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE email email VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
