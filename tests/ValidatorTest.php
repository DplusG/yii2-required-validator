<?php

declare(strict_types=1);

namespace Dgoriaev\Yii2RequiredValidator\Tests;

use Dgoriaev\Yii2RequiredValidator\Tests\Model\RecallModel73;
use Dgoriaev\Yii2RequiredValidator\Tests\Model\RecallModel73WithNewRequiredValidator;
use Dgoriaev\Yii2RequiredValidator\Tests\Model\RecallModel74WithoutHacks;
use Dgoriaev\Yii2RequiredValidator\Tests\Model\RecallModel74Default;
use Dgoriaev\Yii2RequiredValidator\Tests\Model\RecallModel74Hacked;
use PHPUnit\Framework\TestCase;
use Throwable;
use Yii;
use yii\base\Model;

final class ValidatorTest extends TestCase
{
    /**
     * @problem Properties not set. Error instead of validation error
    */
    public function testItHasCriticalErrors(): void
    {
        $this->expectException(Throwable::class);
        $model = new RecallModel74Default();
        $model->setAttributes([]);
        $model->validate();
    }

    /**
     * @dataProvider models
     * @param Model $model
     * @return void
     */
    public function testItHasValidationErrors(Model $model): void
    {
        $model->setAttributes([]);
        $model->validate();

        $expectedErrors = [
            'name' => self::errorMessage($model->getAttributeLabel('name')),
            'phone' => self::errorMessage($model->getAttributeLabel('phone')),
        ];
        $errors = $model->getFirstErrors();

        $this->assertEquals($expectedErrors, $errors);
    }

    /**
     * @dataProvider models
     * @param Model $model
     * @return void
     */
    public function testItHasNoValidationErrors(Model $model): void
    {
        $model->setAttributes([
            'name' => 'asdf',
            'phone' => '555-333',
            'someUnexpectedProperty' => true,
        ]);
        $model->validate();

        $expectedErrors = [];
        $errors = $model->getFirstErrors();

        $this->assertEquals($expectedErrors, $errors);
    }

    public static function errorMessage(string $attributeName): string
    {
        return Yii::t('yii', '{attribute} cannot be blank.', [
            'attribute' => $attributeName
        ]);
    }

    public function models(): array
    {
        if (PHP_VERSION_ID >= 70400) {
            return [
                [new RecallModel73()],
                [new RecallModel74Hacked()],
                [new RecallModel74WithoutHacks()],
                [new RecallModel73WithNewRequiredValidator()],
            ];
        } else {
            return [
                [new RecallModel73()],
                [new RecallModel73WithNewRequiredValidator()],
            ];
        }
    }
}
