<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241230165103 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates table recipe';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE recipe (
                id VARCHAR(36) NOT NULL,
                user_id VARCHAR(36) NOT NULL,
                group_id VARCHAR(36) DEFAULT NULL,
                name VARCHAR(255) NOT NULL,
                category VARCHAR(255) DEFAULT NULL,
                description VARCHAR(500) DEFAULT NULL,
                preparation_time TIME DEFAULT NULL,
                ingredients JSON NOT NULL,
                steps JSON NOT NULL,
                image VARCHAR(255) DEFAULT NULL,
                rating INT DEFAULT NULL,
                public TINYINT(1) DEFAULT 0 NOT NULL,
                created_on DATETIME NOT NULL,

                INDEX idx_id (id),
                INDEX idx_user_id (user_id),
                INDEX idx_group_id (group_id),
                INDEX idx_name (name),
                INDEX idx_category (category),

                PRIMARY KEY(id)
            )
            DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
            ENGINE = InnoDB'
        );

        $this->addSql(
            'ALTER TABLE recipe
                ADD CONSTRAINT FK_DA88B137A76ED395
                    FOREIGN KEY (user_id)
                    REFERENCES `user` (id)'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            'ALTER TABLE recipe
                DROP FOREIGN KEY FK_DA88B137A76ED395'
        );
        $this->addSql('DROP TABLE recipe');
    }
}
