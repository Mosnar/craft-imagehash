<?php
/**
 * Image Hash plugin for Craft CMS 3.x
 *
 * Compute image similarity
 *
 * @link      https://www.venveo.com
 * @copyright Copyright (c) 2020 Venveo
 */

namespace venveo\imagehash;

use Craft;
use craft\base\Plugin;
use craft\elements\Asset;
use craft\events\DefineBehaviorsEvent;
use craft\events\PluginEvent;
use craft\services\Plugins;
use Jenssegers\ImageHash\ImageHash as ImageHasher;
use Jenssegers\ImageHash\Implementations\DifferenceHash;
use venveo\imagehash\behaviors\ImageHashBehavior;
use venveo\imagehash\models\Settings;
use venveo\imagehash\services\ImageHash as ImageHashService;
use yii\base\Event;
use yii\base\ModelEvent;

/**
 * Class ImageHash
 *
 * @author    Venveo
 * @package   ImageHash
 * @since     1.0.0
 *
 * @property  ImageHashService $imageHash
 */
class ImageHash extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var ImageHash
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Event::on(
            Asset::class,
            Asset::EVENT_DEFINE_BEHAVIORS,
            function (DefineBehaviorsEvent $e) {
                $e->behaviors['imageHash'] = ImageHashBehavior::class;
            }
        );

        Event::on(Asset::class,
            Asset::EVENT_BEFORE_SAVE,
            function (\yii\base\ModelEvent $e) {
                /** @var Asset $asset */
                $asset = $e->sender;
                if (!$asset->getBehavior('imageHash')) {
                    return;
                }
                if ($asset->kind !== Asset::KIND_IMAGE ) {
                    $asset->detachBehavior('imageHash');
                    return;
                }

                $hasher = new ImageHasher(new DifferenceHash());
                $hash = $hasher->hash($asset->tempFilePath)->toBits();
                $asset->imageHash = $hash;
            }
        );

        Event::on(Asset::class, Asset::EVENT_BEFORE_VALIDATE, function(ModelEvent $e) {
            /** @var Asset $asset */
            $asset = $e->sender;
            if (!$asset->getBehavior('imageHash')) {
                return;
            }
            $hashRecord = \venveo\imagehash\records\ImageHash::find()->where(['hash' => $asset->imageHash])->one();
            if ($hashRecord) {
                $asset->addError('imageHash', 'An image similar to that already exists on element: '. $hashRecord->assetId);
            }
        });

        Event::on(Asset::class,
            Asset::EVENT_AFTER_SAVE,
            function (\yii\base\ModelEvent $e) {
                /** @var Asset $asset */
                $asset = $e->sender;
                if ($asset->scenario === Asset::SCENARIO_FILEOPS || $asset->scenario === Asset::SCENARIO_CREATE) {
                    return;
                }

                if (!$asset->getBehavior('imageHash')) {
                    return;
                }

                $record = new \venveo\imagehash\records\ImageHash([
                    'hash' => $asset->imageHash,
                    'assetId' => $asset->id
                ]);
                $record->save();
                if ($record->hasErrors()) {
                    Craft::dd($record->getErrors());
                }
            }
        );
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'image-hash/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }
}
