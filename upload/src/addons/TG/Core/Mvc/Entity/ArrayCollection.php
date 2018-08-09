<?php

namespace TG\Core\Mvc\Entity;

class ArrayCollection extends \XF\Mvc\Entity\ArrayCollection
{
	public function __get($key)
	{
		return $this->offsetGet($key);
	}

	public function __set($key, $value)
	{
		$this->offsetSet($key, $value);
	}

	public function __isset($key)
	{
		return $this->offsetExists($key);
	}
}