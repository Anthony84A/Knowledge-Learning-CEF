<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250912091504 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certification ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, DROP created_by, DROP updated_by');
        $this->addSql('ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D75B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D75896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_6C3C6D75B03A8386 ON certification (created_by_id)');
        $this->addSql('CREATE INDEX IDX_6C3C6D75896DBBDE ON certification (updated_by_id)');
        $this->addSql('ALTER TABLE cursus ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, DROP created_by, DROP updated_by');
        $this->addSql('ALTER TABLE cursus ADD CONSTRAINT FK_255A0C3B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE cursus ADD CONSTRAINT FK_255A0C3896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_255A0C3B03A8386 ON cursus (created_by_id)');
        $this->addSql('CREATE INDEX IDX_255A0C3896DBBDE ON cursus (updated_by_id)');
        $this->addSql('ALTER TABLE lesson ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, DROP created_by, DROP updated_by');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F3B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F3896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_F87474F3B03A8386 ON lesson (created_by_id)');
        $this->addSql('CREATE INDEX IDX_F87474F3896DBBDE ON lesson (updated_by_id)');
        $this->addSql('ALTER TABLE purchase ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, DROP created_by, DROP updated_by');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13BB03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13B896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_6117D13BB03A8386 ON purchase (created_by_id)');
        $this->addSql('CREATE INDEX IDX_6117D13B896DBBDE ON purchase (updated_by_id)');
        $this->addSql('ALTER TABLE theme ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, DROP created_by, DROP updated_by');
        $this->addSql('ALTER TABLE theme ADD CONSTRAINT FK_9775E708B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE theme ADD CONSTRAINT FK_9775E708896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_9775E708B03A8386 ON theme (created_by_id)');
        $this->addSql('CREATE INDEX IDX_9775E708896DBBDE ON theme (updated_by_id)');
        $this->addSql('ALTER TABLE user ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, DROP created_by, DROP updated_by');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649B03A8386 ON user (created_by_id)');
        $this->addSql('CREATE INDEX IDX_8D93D649896DBBDE ON user (updated_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D75B03A8386');
        $this->addSql('ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D75896DBBDE');
        $this->addSql('DROP INDEX IDX_6C3C6D75B03A8386 ON certification');
        $this->addSql('DROP INDEX IDX_6C3C6D75896DBBDE ON certification');
        $this->addSql('ALTER TABLE certification ADD created_by VARCHAR(255) DEFAULT NULL, ADD updated_by VARCHAR(255) DEFAULT NULL, DROP created_by_id, DROP updated_by_id');
        $this->addSql('ALTER TABLE cursus DROP FOREIGN KEY FK_255A0C3B03A8386');
        $this->addSql('ALTER TABLE cursus DROP FOREIGN KEY FK_255A0C3896DBBDE');
        $this->addSql('DROP INDEX IDX_255A0C3B03A8386 ON cursus');
        $this->addSql('DROP INDEX IDX_255A0C3896DBBDE ON cursus');
        $this->addSql('ALTER TABLE cursus ADD created_by VARCHAR(255) DEFAULT NULL, ADD updated_by VARCHAR(255) DEFAULT NULL, DROP created_by_id, DROP updated_by_id');
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F3B03A8386');
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F3896DBBDE');
        $this->addSql('DROP INDEX IDX_F87474F3B03A8386 ON lesson');
        $this->addSql('DROP INDEX IDX_F87474F3896DBBDE ON lesson');
        $this->addSql('ALTER TABLE lesson ADD created_by VARCHAR(255) DEFAULT NULL, ADD updated_by VARCHAR(255) DEFAULT NULL, DROP created_by_id, DROP updated_by_id');
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13BB03A8386');
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13B896DBBDE');
        $this->addSql('DROP INDEX IDX_6117D13BB03A8386 ON purchase');
        $this->addSql('DROP INDEX IDX_6117D13B896DBBDE ON purchase');
        $this->addSql('ALTER TABLE purchase ADD created_by VARCHAR(255) DEFAULT NULL, ADD updated_by VARCHAR(255) DEFAULT NULL, DROP created_by_id, DROP updated_by_id');
        $this->addSql('ALTER TABLE theme DROP FOREIGN KEY FK_9775E708B03A8386');
        $this->addSql('ALTER TABLE theme DROP FOREIGN KEY FK_9775E708896DBBDE');
        $this->addSql('DROP INDEX IDX_9775E708B03A8386 ON theme');
        $this->addSql('DROP INDEX IDX_9775E708896DBBDE ON theme');
        $this->addSql('ALTER TABLE theme ADD created_by VARCHAR(255) DEFAULT NULL, ADD updated_by VARCHAR(255) DEFAULT NULL, DROP created_by_id, DROP updated_by_id');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649B03A8386');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649896DBBDE');
        $this->addSql('DROP INDEX IDX_8D93D649B03A8386 ON `user`');
        $this->addSql('DROP INDEX IDX_8D93D649896DBBDE ON `user`');
        $this->addSql('ALTER TABLE `user` ADD created_by VARCHAR(255) DEFAULT NULL, ADD updated_by VARCHAR(255) DEFAULT NULL, DROP created_by_id, DROP updated_by_id');
    }
}
