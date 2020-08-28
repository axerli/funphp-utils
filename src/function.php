<?php

declare(strict_types = 1);

use Funphp\Utils\ArrayHelper;
use Funphp\Utils\Collection;

if (!function_exists('value')) {
	/**
	 * @param $value
	 * @return mixed
	 */
	function value($value)
	{
		return $value instanceof Closure ? $value() : $value;
	}
}

if (!function_exists('last')) {
	/**
	 * get the last element
	 * @param $array
	 * @return mixed
	 */
	function last($array)
	{
		return end($array);
	}
}

if (!function_exists('first')) {
	/**
	 * get the first element
	 * @param $array
	 * @return mixed
	 */
	function first($array)
	{
		return reset($array);
	}
}

if (! function_exists('data_get')) {
	/**
	 * Get an item from an array or object using "dot" notation.
	 *
	 * @param array|int|string $key
	 * @param null|mixed       $default
	 * @param mixed            $target
	 * @return array|mixed
	 */
	function data_get($target, $key, $default = null)
	{
		if ($key === null) {
			return $target;
		}

		$key = is_array($key) ? $key : explode('.', is_int($key) ? (string) $key : $key);
		while (! is_null($segment = array_shift($key))) {
			if ($segment === '*') {
				if ($target instanceof Collection) {
					$target = $target->all();
				} elseif (! is_array($target)) {
					return value($default);
				}
				$result = [];
				foreach ($target as $item) {
					$result[] = data_get($item, $key);
				}
				return in_array('*', $key, true) ? ArrayHelper::collapse($result) : $result;
			}
			if (ArrayHelper::accessible($target) && ArrayHelper::exists($target, $segment)) {
				$target = $target[$segment];
			} elseif (is_object($target) && isset($target->{$segment})) {
				$target = $target->{$segment};
			} else {
				return value($default);
			}
		}
		return $target;
	}
}
if (! function_exists('data_set')) {
	/**
	 * Set an item on an array or object using dot notation.
	 *
	 * @param mixed        $target
	 * @param array|string $key
	 * @param bool         $overwrite
	 * @param mixed        $value
	 * @return array|mixed
	 */
	function data_set(&$target, $key, $value, $overwrite = true)
	{
		$segments = is_array($key) ? $key : explode('.', $key);
		if (($segment = array_shift($segments)) === '*') {
			if (! ArrayHelper::accessible($target)) {
				$target = [];
			}
			if ($segments) {
				foreach ($target as &$inner) {
					data_set($inner, $segments, $value, $overwrite);
				}
			} elseif ($overwrite) {
				foreach ($target as &$inner) {
					$inner = $value;
				}
			}
		} elseif (ArrayHelper::accessible($target)) {
			if ($segments) {
				if (! ArrayHelper::exists($target, $segment)) {
					$target[$segment] = [];
				}
				data_set($target[$segment], $segments, $value, $overwrite);
			} elseif ($overwrite || ! ArrayHelper::exists($target, $segment)) {
				$target[$segment] = $value;
			}
		} elseif (is_object($target)) {
			if ($segments) {
				if (! isset($target->{$segment})) {
					$target->{$segment} = [];
				}
				data_set($target->{$segment}, $segments, $value, $overwrite);
			} elseif ($overwrite || ! isset($target->{$segment})) {
				$target->{$segment} = $value;
			}
		} else {
			$target = [];
			if ($segments) {
				data_set($target[$segment], $segments, $value, $overwrite);
			} elseif ($overwrite) {
				$target[$segment] = $value;
			}
		}
		return $target;
	}
}

if (!function_exists('collect')) {
	function collect($data = null)
	{
		return new Collection($data);
	}
}

if (!function_exists('tap')) {
	function tap($value, callable $callback = null)
	{
		if (!$callback) {
			return $value;
		}

		$callback($value);

		return $value;
	}
}

if (!function_exists('with')) {
	function with($value, callable $callback = null)
	{
		return $callback ? $callback($value) : $value;
	}
}