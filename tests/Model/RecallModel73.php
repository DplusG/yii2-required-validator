<?php

declare(strict_types=1);

namespace Dgoriaev\Yii2RequiredValidator\Tests\Model;

use yii\base\Model;

final class RecallModel73 extends Model
{
	public $name;
	public $phone;
    public $optional;

	public function rules(): array
	{
		return [
			[['name', 'phone'], 'required'],
		];
	}
}