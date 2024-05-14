<?php

declare(strict_types=1);

namespace Dgoriaev\Yii2RequiredValidator\Tests\Model;

use Dgoriaev\Yii2RequiredValidator\RequiredValidator;
use yii\base\Model;

final class RecallModel73WithNewRequiredValidator extends Model
{
	public $name;
	public $phone;
    public $optional;

	public function rules(): array
	{
		return [
			[['name', 'phone'], RequiredValidator::class],
		];
	}
}