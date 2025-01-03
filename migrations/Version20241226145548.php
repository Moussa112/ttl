<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241226145548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE removed_item DROP FOREIGN KEY FK_912C36CAD6C84247');
        $this->addSql('DROP INDEX IDX_912C36CAD6C84247 ON removed_item');
        $this->addSql('ALTER TABLE removed_item ADD basket_id INT NOT NULL, CHANGE basket_item_id product_id INT NOT NULL');
        $this->addSql('ALTER TABLE removed_item ADD CONSTRAINT FK_912C36CA4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE removed_item ADD CONSTRAINT FK_912C36CA1BE1FB52 FOREIGN KEY (basket_id) REFERENCES basket (id)');
        $this->addSql('CREATE INDEX IDX_912C36CA4584665A ON removed_item (product_id)');
        $this->addSql('CREATE INDEX IDX_912C36CA1BE1FB52 ON removed_item (basket_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE removed_item DROP FOREIGN KEY FK_912C36CA4584665A');
        $this->addSql('ALTER TABLE removed_item DROP FOREIGN KEY FK_912C36CA1BE1FB52');
        $this->addSql('DROP INDEX IDX_912C36CA4584665A ON removed_item');
        $this->addSql('DROP INDEX IDX_912C36CA1BE1FB52 ON removed_item');
        $this->addSql('ALTER TABLE removed_item ADD basket_item_id INT NOT NULL, DROP product_id, DROP basket_id');
        $this->addSql('ALTER TABLE removed_item ADD CONSTRAINT FK_912C36CAD6C84247 FOREIGN KEY (basket_item_id) REFERENCES basket_item (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_912C36CAD6C84247 ON removed_item (basket_item_id)');
    }
}
