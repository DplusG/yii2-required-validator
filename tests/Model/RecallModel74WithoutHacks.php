<?php

declare(strict_types=1);

namespace Dgoriaev\Yii2RequiredValidator\Tests\Model;

use Dgoriaev\Yii2RequiredValidator\RequiredValidator;
use yii\base\Model;

final class RecallModel74WithoutHacks extends Model
{
	public string $name;
	public string $phone;
    public ?string $optional = null;

	public function rules(): array
	{
		return [
			[['name', 'phone'], RequiredValidator::class],
		];
	}
}