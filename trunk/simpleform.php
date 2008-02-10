<?php

/**
 * SimpleForm v3 base include file, initializes classloading environment
 * @author Lachlan Donald <lachlan@sitepoint.com>
 */

require_once(dirname(__FILE__).'/classes/SimpleForm/ClassLoader.php');
require_once(dirname(__FILE__).'/classes/SimpleForm/Exception.php');

// create a classloader
$classLoader = new SimpleForm_ClassLoader();

?>
