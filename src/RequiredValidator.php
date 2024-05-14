<?php
/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace Dgoriaev\Yii2RequiredValidator;

use ReflectionException;
use ReflectionProperty;
use Yii;
use yii\helpers\Json;
use yii\validators\ValidationAsset;
use yii\validators\Validator;

/**
 * RequiredValidator validates that the specified attribute does not have null or empty value.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class RequiredValidator extends Validator
{
    /**
     * @var bool whether to skip this validator if the value being validated is empty.
     */
    public $skipOnEmpty = false;
    /**
     * @var mixed the desired value that the attribute must have.
     * If this is null, the validator will validate that the specified attribute is not empty.
     * If this is set as a value that is not null, the validator will validate that
     * the attribute has a value that is the same as this property value.
     * Defaults to null.
     * @see strict
     */
    public $requiredValue;
    /**
     * @var bool whether the comparison between the attribute value and [[requiredValue]] is strict.
     * When this is true, both the values and types must match.
     * Defaults to false, meaning only the values need to match.
     *
     * Note that behavior for when [[requiredValue]] is null is the following:
     *
     * - In strict mode, the validator will check if the attribute value is null
     * - In non-strict mode validation will fail
     */
    public $strict = false;
    /**
     * @var string the user-defined error message. It may contain the following placeholders which
     * will be replaced accordingly by the validator:
     *
     * - `{attribute}`: the label of the attribute being validated
     * - `{value}`: the value of the attribute being validated
     * - `{requiredValue}`: the value of [[requiredValue]]
     */
    public $message;


    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = $this->requiredValue === null ? Yii::t('yii', '{attribute} cannot be blank.')
                : Yii::t('yii', '{attribute} must be "{requiredValue}".');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function validateValue($value)
    {
        if ($this->requiredValue === null) {
            if ($this->strict && $value !== null || !$this->strict && !$this->isEmpty(is_string($value) ? trim($value) : $value)) {
                return null;
            }
        } elseif (!$this->strict && $value == $this->requiredValue || $this->strict && $value === $this->requiredValue) {
            return null;
        }
        if ($this->requiredValue === null) {
            return [$this->message, []];
        }

        return [$this->message, [
            'requiredValue' => $this->requiredValue,
        ]];
    }

    /**
     * {@inheritdoc}
     */
    public function clientValidateAttribute($model, $attribute, $view)
    {
        ValidationAsset::register($view);
        $options = $this->getClientOptions($model, $attribute);

        return 'yii.validation.required(value, messages, ' . Json::htmlEncode($options) . ');';
    }

    /**
     * {@inheritdoc}
     */
    public function getClientOptions($model, $attribute)
    {
        $options = [];
        if ($this->requiredValue !== null) {
            $options['message'] = $this->formatMessage($this->message, [
                'requiredValue' => $this->requiredValue,
            ]);
            $options['requiredValue'] = $this->requiredValue;
        } else {
            $options['message'] = $this->message;
        }
        if ($this->strict) {
            $options['strict'] = 1;
        }

        $options['message'] = $this->formatMessage($options['message'], [
            'attribute' => $model->getAttributeLabel($attribute),
        ]);

        return $options;
    }

    /**
     * @new
     * @param $model
     * @param $attribute
     * @return void
     * @throws \yii\base\NotSupportedException
     */
    public function validateAttribute($model, $attribute)
    {
        if (PHP_VERSION_ID >= 70400 && !$this->validateInitialized($model, $attribute)) {
            return;
        }

        parent::validateAttribute($model, $attribute);
    }

    /**
     * @new
     * Adds an error about the specified attribute to the model object.
     * This is a helper method that performs message selection and internationalization.
     * @param \yii\base\Model $model the data model being validated
     * @param string $attribute the attribute being validated
     * @param string $message the error message
     * @param array $params values for the placeholders in the error message
     */
    public function addError($model, $attribute, $message, $params = [])
    {
        if (PHP_VERSION_ID >= 70400 && !self::isInitialized($model, $attribute)) {
            $params['attribute'] = $model->getAttributeLabel($attribute);
            $params['value'] = null;
            $model->addError($attribute, $this->formatMessage($message, $params));
            return;
        }

        parent::addError($model, $attribute, $message, $params);
    }

    /**
     * @new
     * @param $model
     * @param $attribute
     * @return bool
     */
    public function validateInitialized($model, $attribute): bool
    {
        $isInitialized = self::isInitialized($model, $attribute);

        if ($isInitialized) {
            return true;
        }

        if (is_null($isInitialized)) {
            $this->addError($model, $attribute, Yii::t('yii', 'An internal server error occurred.'));
        } else {
            $this->addError($model, $attribute, $this->message);
        }

        return false;
    }

    /**
     * @new
     * @param $model
     * @param $attribute
     * @return bool|null
     */
    private static function isInitialized($model, $attribute): ?bool
    {
        try {
            $rp = new ReflectionProperty(get_class($model), $attribute);

            return $rp->isInitialized($model);
        } catch (ReflectionException $exception) {
            return null;
        }
    }
}
