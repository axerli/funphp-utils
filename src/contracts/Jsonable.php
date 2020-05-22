<?php

declare(strict_types = 1);

namespace Funphp\Utils\Contracts;

interface Jsonable
{
	public function toJson(int $options = 0);
}