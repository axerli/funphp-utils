<?php

declare(strict_types = 1);

namespace Funphp\Utils;


/**
 * thanks Laravel provide such a useful class.
 *
 * @mixin Collection
 */
class HigherOrderCollectionProxy
{
	/**
	 * The collection being operated on.
	 *
	 * @var Collection
	 */
	protected $collection;

	/**
	 * The method being proxied.
	 *
	 * @var string
	 */
	protected $method;

	/**
	 * Create a new proxy instance.
	 * @param Collection $collection
	 * @param string     $method
	 */
	public function __construct(Collection $collection, string $method)
	{
		$this->method     = $method;
		$this->collection = $collection;
	}

	public function __isset($name)
	{
	}

	public function __set($name, $value)
	{
	}


	/**
	 * Proxy accessing an attribute onto the collection items.
	 * @param string $key
	 * @return mixed
	 */
	public function __get(string $key)
	{
		return $this->collection->{$this->method}(function ($value) use ($key) {
			return is_array($value) ? $value[$key] : $value->{$key};
		});
	}

	/**
	 * Proxy a method call onto the collection items.
	 * @param string $method
	 * @param array  $parameters
	 * @return mixed
	 */
	public function __call(string $method, array $parameters)
	{
		return $this->collection->{$this->method}(function ($value) use ($method, $parameters) {
			return $value->{$method}(...$parameters);
		});
	}
}
