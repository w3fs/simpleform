<?php

/**
 * A simple interface to the HTTP request
 */
class SimpleForm_HttpRequest
{
	const METHOD_GET='get';
	const METHOD_POST='post';
	const METHOD_AUTO='auto';

	/**
	 * Gets the method
	 */
	public function getMethod()
	{
		return strtolower($_SERVER['REQUEST_METHOD']);
	}

	/**
	 * Gets all request variables as a key=>value array. The method param
	 * specifies whether to get POST or GET variables, or auto, which gets
	 * the parameters that match the method of form submission.
	 * @param $method which type of variables to fetch
	 * @return array the parameters requested
	 */
	public function getParams($method=self::METHOD_AUTO)
	{
		if($method == self::METHOD_AUTO)
		{
			return ($this->getMethod() == self::METHOD_POST) ? $_POST : $_GET;
		}
		else if($method == self::METHOD_POST)
		{
			return $_POST;
		}
		else
		{
			return $_GET;
		}
	}

	/**
	 * Gets a particular request variable. The method param
	 * specifies whether to get POST or GET variables, or auto, which gets
	 * the parameters that match the method of form submission.
	 * @param $method which type of variables to fetch
	 * @param $default a default value if the parameter isn't set
	 * @return array the parameters requested
	 */
	public function getParam($key, $default=false, $method=self::METHOD_AUTO)
	{
		$vars = $this->getParams($method);
		return isset($vars[$key]) ? $vars[$key] : $default;
	}

	/**
	 * Gets the full URI of the request
	 */
	public function getUri()
	{
		return $_SERVER['REQUEST_URI'];
	}

	/**
	 * Gets an array of uploaded files, like $_FILES
	 */
	public function getUploadedFiles()
	{
		return $_FILES;
	}
}

?>
