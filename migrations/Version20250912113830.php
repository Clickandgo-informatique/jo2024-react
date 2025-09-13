<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250912113830 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categories_offres (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commandes (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, reference VARCHAR(20) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', payee TINYINT(1) NOT NULL, payee_le DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_35D4282CAEA34913 (reference), INDEX IDX_35D4282CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE details_commandes (commande_id INT NOT NULL, offres_id INT NOT NULL, quantite INT NOT NULL, prix INT NOT NULL, INDEX IDX_4FD424F782EA2E54 (commande_id), INDEX IDX_4FD424F76C83CD9F (offres_id), PRIMARY KEY(commande_id, offres_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE images (id INT AUTO_INCREMENT NOT NULL, offres_id INT NOT NULL, nom VARCHAR(255) NOT NULL, INDEX IDX_E01FBE6A6C83CD9F (offres_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offres (id INT AUTO_INCREMENT NOT NULL, intitule VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, description LONGTEXT DEFAULT NULL, code VARCHAR(8) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', nbr_adultes INT NOT NULL, nbr_enfants INT NOT NULL, is_locked TINYINT(1) NOT NULL, is_published TINYINT(1) NOT NULL, slug VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, nickname VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_IDENTIFIER_NICKNAME (nickname), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commandes ADD CONSTRAINT FK_35D4282CA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE details_commandes ADD CONSTRAINT FK_4FD424F782EA2E54 FOREIGN KEY (commande_id) REFERENCES commandes (id)');
        $this->addSql('ALTER TABLE details_commandes ADD CONSTRAINT FK_4FD424F76C83CD9F FOREIGN KEY (offres_id) REFERENCES offres (id)');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A6C83CD9F FOREIGN KEY (offres_id) REFERENCES offres (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commandes DROP FOREIGN KEY FK_35D4282CA76ED395');
        $this->addSql('ALTER TABLE details_commandes DROP FOREIGN KEY FK_4FD424F782EA2E54');
        $this->addSql('ALTER TABLE details_commandes DROP FOREIGN KEY FK_4FD424F76C83CD9F');
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A6C83CD9F');
        $this->addSql('DROP TABLE categories_offres');
        $this->addSql('DROP TABLE commandes');
        $this->addSql('DROP TABLE details_commandes');
        $this->addSql('DROP TABLE images');
        $this->addSql('DROP TABLE offres');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
