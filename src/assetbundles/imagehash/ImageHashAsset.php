<?php
/**
 * Image Hash plugin for Craft CMS 3.x
 *
 * Compute image similarity
 *
 * @link      https://www.venveo.com
 * @copyright Copyright (c) 2020 Venveo
 */

namespace venveo\imagehash\assetbundles\ImageHash;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Venveo
 * @package   ImageHash
 * @since     1.0.0
 */
class ImageHashAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@venveo/imagehash/assetbundles/imagehash/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/ImageHash.js',
        ];

        $this->css = [
            'css/ImageHash.css',
        ];

        parent::init();
    }
}
