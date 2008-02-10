<?php

/**
 * A helper to collapse and expand arrays. This allows PHP nested POST
 * arrays to be converted to the simpleform required key=>value pair, e.g:
 *
 * <code>
 * 'element[a][b]' => 'value'   // this is the collapsed form
 * element = array('a'=>array('b'=>'value')) // this is the expanded form
 * </code>
 */
class SimpleForm_FormArrayHelper
{
	/**
	 * Expands a key of flat key=>value pairs to an expanded array
	 */
	public static function expand(array $array)
	{
		$newArray = array();

		foreach($array as $key=>$value)
		{
			// if the key contains [] syntax, transformation is needed
			if(preg_match('/^(.+?)(\[.+?\])$/',$key,$m))
			{
				$current =& $newArray[$m[1]];
				$stack = preg_split('/\]\[/',trim($m[2],'[]'));

				// iterate down the array, leaving $current as the leaf
				foreach($stack as $item) $current =& $current[$item];

				$current = $value;
			}
			else
			{
				$newArray[$key] = $value;
			}
		}

		return $newArray;
	}

	/**
	 * Collapse a nested array down to an array of flat key=>value pairs
	 */
	public static function collapse(array $array)
	{
		$newArray = array();

		foreach($array as $key=>$value)
		{
			if(is_array($value))
			{
				// strip any manually added []
				if(preg_match('/\[\]$/',$key)) $key = substr($key,0,-2);

				self::_recurseCollapse($value,$newArray,array($key));
			}
			else
			{
				$newArray[$key] = $value;
			}
		}

		return $newArray;
	}

	/**
	 * Recurse through an array, add the leaf items to the $newArray var
	 */
	private static function _recurseCollapse($subject,&$newArray,$stack=array())
	{
		foreach($subject as $key=>$value)
		{
			$fstack = array_merge($stack,array($key));

			if(is_array($value))
			{
				self::_recurseCollapse($value,$newArray,$fstack);
			}
			else
			{
				$top = array_shift($fstack);
				$arrayPart = count($fstack) ? '[' . implode('][',$fstack) .']' : '';
				$newArray[$top . $arrayPart] = $value;
			}
		}
	}
}

?>

