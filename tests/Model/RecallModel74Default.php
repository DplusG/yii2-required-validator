<?php

declare(strict_types=1);

namespace Dgoriaev\Yii2RequiredValidator\Tests\Model;

use yii\base\Model;

final class RecallModel74Default extends Model
{
	public string $name;
	public string $phone;
    public ?string $optional = null;

	public function rules(): array
	{
		return [
			[['name', 'phone'], 'required'],
		];
	}
}