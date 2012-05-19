<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Array_Comparer_MuliteSubKey
{

	var $keys;
	var $sort_desc = false;

	function __construct($keys)
	{
		if (is_array($keys))
		{
			$this->keys = (array)$keys;
		}
		else
		{
			$this->keys = func_get_args();
		}

		return $this;
	}

	function newInstance($keys)
	{
		if (is_array($keys))
		{
			$keys = (array)$keys;
		}
		else
		{
			$keys = func_get_args();
		}

		return new self($keys);
	}

	function sort_desc($sort_desc = null)
	{
		if ($sort_desc !== null)
		{
			$this->sort_desc = $sort_desc;
		}

		return $this;
	}

	function compare($a, $b)
	{
		$i = 0;
		$c = count((array)$this->keys);

		$cmp = 0;
		while ($cmp == 0 && $i < $c)
		{
			$cmp = strcmp($a[$this->keys[$i]], $b[$this->keys[$i]]);

			if ($this->sort_desc)
			{
				$cmp = 0 - $cmp;
			}

			$i++;
		}

		return $cmp;
	}

}
