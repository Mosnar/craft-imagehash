<?php
/**
 * Image Hash plugin for Craft CMS 3.x
 *
 * Compute image similarity
 *
 * @link      https://www.venveo.com
 * @copyright Copyright (c) 2020 Venveo
 */

namespace venveo\imagehash\migrations;

use venveo\imagehash\ImageHash;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;

/**
 * @author    Venveo
 * @package   ImageHash
 * @since     1.0.0
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================

    /**
     * @var string The database driver to use
     */
    public $driver;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            $this->createIndexes();
            $this->addForeignKeys();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
            $this->insertDefaultData();
        }

        return true;
    }

   /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->removeTables();
        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * @return bool
     */
    protected function createTables()
    {
        $tablesCreated = false;

        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%imagehash_imagehash}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%imagehash_imagehash}}',
                [
                    'id' => $this->primaryKey(),
                    'assetId' => $this->integer()->notNull(),
                    'hash' => $this->string(64)->notNull(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                ]
            );
        }

        return $tablesCreated;
    }

    /**
     * @return void
     */
    protected function createIndexes()
    {
        $this->createIndex(
            $this->db->getIndexName(
                '{{%imagehash_imagehash}}',
                'hash',
                true
            ),
            '{{%imagehash_imagehash}}',
            'hash',
            true
        );
    }

    /**
     * @return void
     */
    protected function addForeignKeys()
    {
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%imagehash_imagehash}}', 'assetId'),
            '{{%imagehash_imagehash}}',
            'siteId',
            '{{%assets}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * @return void
     */
    protected function insertDefaultData()
    {
    }

    /**
     * @return void
     */
    protected function removeTables()
    {
        $this->dropTableIfExists('{{%imagehash_imagehash}}');
    }
}
