<?php

/**
 * Provides configuration settings for the test suites
 */

/**
 * The location of the SimpleTest project, with trailing slash. Needs
 * 1.01beta2 or better.
 */
define('SIMPLETEST_PATH','/Users/lachlan/Documents/Projects/simpletest-1.01beta2/');

/**
 * The maximum memory that can be allocated during the execution of a test suite
 */
define('TEST_MEMORYLIMIT','64M');

/**
 * The location of the SimpleForm base directory
 */
define('SIMPLEFORM_PATH',dirname(__FILE__).'/../');

/**
 * The location of the SimpleForm clases, with trailing slash
 */
define('SIMPLEFORM_CLASSPATH',SIMPLEFORM_PATH.'classes/');

/**
 * The location of the SimpleForm tests directory, with trailing slash
 */
define('SIMPLEFORM_TESTPATH',SIMPLEFORM_PATH.'tests/');

/**
 * The location of the SimpleForm tests/forms directory, with trailing slash
 */
define('SIMPLEFORM_TESTFORMS',SIMPLEFORM_PATH.'tests/forms/');

?>
