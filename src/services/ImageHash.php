<?php
/**
 * Image Hash plugin for Craft CMS 3.x
 *
 * Compute image similarity
 *
 * @link      https://www.venveo.com
 * @copyright Copyright (c) 2020 Venveo
 */

namespace venveo\imagehash\services;

use venveo\imagehash\ImageHash;

use Craft;
use craft\base\Component;

/**
 * @author    Venveo
 * @package   ImageHash
 * @since     1.0.0
 */
class ImageHash extends Component
{
    // Public Methods
    // =========================================================================

    /*
     * @return mixed
     */
    public function exampleService()
    {
        $result = 'something';
        // Check our Plugin's settings for `someAttribute`
        if (ImageHash::$plugin->getSettings()->someAttribute) {
        }

        return $result;
    }
}
