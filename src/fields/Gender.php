<?php
/**
 * NSM Fields plugin for Craft CMS 3.x
 *
 * Various fields for CraftCMS
 *
 * @link      http://newism.com.au
 * @copyright Copyright (c) 2017 Leevi Graham
 */

namespace newism\fields\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\base\PreviewableFieldInterface;
use newism\fields\models\GenderModel;
use RuntimeException;
use Twig_Error_Loader;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\db\Schema;

/**
 * Gender Field
 *
 * Whenever someone creates a new field in Craft, they must specify what
 * type of field it is. The system comes with a handful of field types baked in,
 * and we’ve made it extremely easy for plugins to add new ones.
 *
 * https://craftcms.com/docs/plugins/field-types
 *
 * @author    Leevi Graham
 * @package   NsmFields
 * @since     1.0.0
 */
class Gender extends Field implements PreviewableFieldInterface
{
    // Static Methods
    // =========================================================================

    /**
     * Returns the display name of this class.
     *
     * @return string The display name of this class.
     */
    public static function displayName(): string
    {
        return Craft::t('nsm-fields', 'NSM Gender');
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        $rules = parent::rules();
        $rules = array_merge(
            $rules,
            []
        );

        return $rules;
    }

    /**
     * Get settings HTML
     *
     * @return string
     * @throws Exception
     * @throws Twig_Error_Loader
     * @throws RuntimeException
     */
    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate(
            'nsm-fields/_components/fieldtypes/Gender/settings',
            [
                'field' => $this,
            ]
        );
    }

    /**
     * @return string
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_TEXT;
    }

    /**
     * @param mixed $value
     * @param ElementInterface|null $element
     * @return mixed|GenderModel
     */
    public function normalizeValue(
        $value,
        ElementInterface $element = null
    ) {
        /**
         * Just return value if it's already an GenderModel.
         */
        if ($value instanceof GenderModel) {
            return $value;
        }

        /**
         * Serialised value from the DB
         */
        if (is_string($value)) {
            $value = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        }

        /**
         * Array value from post or unserialized array
         */
        if (is_array($value) && !empty(array_filter($value))) {
            return new GenderModel($value);
        }

        return null;
    }

    /**
     * Returns the field’s input HTML.
     *
     * @param mixed $value
     * @param ElementInterface|null $element
     * @return string
     * @throws InvalidParamException
     * @throws Exception
     * @throws Twig_Error_Loader
     * @throws RuntimeException
     * @throws InvalidConfigException
     */
    public function getInputHtml(
        $value,
        ElementInterface $element = null
    ): string {
        return Craft::$app->getView()->renderTemplate(
            'nsm-fields/_components/fieldtypes/Gender/input',
            [
                'name' => $this->handle,
                'value' => $value,
                'field' => $this,
                'sexOptions' => $this->getSexOptions(),
            ]
        );
    }

    protected function getSexOptions()
    {
        return [
            '' => '',
            'M' => 'Male',
            'F' => 'Female',
            'O' => 'Other',
            'N' => 'None',
            'U' => 'Unknown',
        ];
    }

    /**
     * @param mixed $value
     * @param ElementInterface $element
     * @return string
     */
    public function getSearchKeywords(
        $value,
        ElementInterface $element
    ): string {
        return json_encode($this);
    }

    /**
     * Returns whether the given value should be considered “empty” to a validator.
     *
     * @param mixed $value The field’s value
     * @param ElementInterface $element
     *
     * @return bool Whether the value should be considered “empty”
     * @see Validator::$isEmpty
     */
    public function isValueEmpty($value, ElementInterface $element = null): bool
    {
        if ($value instanceof GenderModel) {
            return $value->isEmpty();
        }

        return parent::isValueEmpty($value, $element);
    }

}
