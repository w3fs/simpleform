<?php

/**
 * A generic class loader for interacting the the spl autoload api. Assumes
 * classes are in the PEAR style, where underscores are converted into directories
 *
 * @author Lachlan Donald <lachlan@ljd.cc>
 */
class SimpleForm_ClassLoader
{
	private $_basedir;
	private $_registered=false;

	/**
	 * Creates the class loader
	 *
	 * @param $basedir mixed either the base of the classes, or if false guesses
	 * @param $register bool whether to automatically register self with php
	 */
	public function __construct($basedir=false,$register=true)
	{
		$this->_basedir = $basedir ? $basedir : dirname(__FILE__).'/../';
		if($register) $this->register();
	}

	/**
	 * Registers the classloader with php via {@link spl_autoload_register}
	 * @throws SimpleForm_Exception if the class is already registered
	 */
	public function register()
	{
		if($this->_registered)
		{
			throw new SimpleForm_Exception("Classloader is already registered");
		}

		spl_autoload_register(array($this,'load'));
		$this->_registered = true;
	}

	/**
	 * Loads the class, returning true if it loaded, false otherwise
	 * return bool true if the class loaded successfully
	 */
	public function load($class)
	{
		if(!$this->isLoadable($class))
		{
			return false;
		}
		else
		{
			return require($this->_getClassFilename($class));
		}
	}

	/**
	 * Determines if a class is loaded, analagous to {@link class_exists}
	 * @return bool true if the class exists
	 */
	public function isLoaded($class)
	{
		return class_exists($class);
	}

	/**
	 * Determines if a class can be loaded by checking for the existance of
	 * the class file
	 * @return bool true if the classfile exists
	 */
	public function isLoadable($class)
	{
		return file_exists($this->_getClassFilename($class));
	}

	/**
	 * Gets the filename for a classname
	 */
	private function _getClassFilename($class)
	{
		return sprintf('%s%s.php',
			$this->_basedir,
			str_replace('_',DIRECTORY_SEPARATOR,$class));
	}
}

?>
