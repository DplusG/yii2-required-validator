<?php

declare(strict_types=1);

namespace Dgoriaev\Yii2RequiredValidator\Tests\Model;

use yii\base\Model;

final class RecallModel74Hacked extends Model
{
	public ?string $name = null;
	public ?string $phone = null;
	public ?string $optional = null;

	public function rules(): array
	{
		return [
			[['name', 'phone'], 'required'],
		];
	}
}