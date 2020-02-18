<?php
/**
 * Image Hash plugin for Craft CMS 3.x
 *
 * Compute image similarity
 *
 * @link      https://www.venveo.com
 * @copyright Copyright (c) 2020 Venveo
 */

namespace venveo\imagehash\records;

use craft\db\ActiveRecord;

/**
 * @author    Venveo
 * @package   ImageHash
 * @since     1.0.0
 */
class ImageHash extends ActiveRecord
{
    // Public Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%imagehash_imagehash}}';
    }
}
