<?php

/**
 * Runs all of the tests
 * @author Lachlan Donald
 */

require_once(dirname(__FILE__).'/testconfig.php');
require_once(SIMPLEFORM_PATH.'simpleform.php');
require_once(SIMPLETEST_PATH.'unit_tester.php');
require_once(SIMPLETEST_PATH.'reporter.php');
require_once(SIMPLETEST_PATH.'mock_objects.php');

// set up basic parameters
error_reporting(E_ALL);
ini_set("memory_limit", TEST_MEMORYLIMIT);

// set up test class loading
$testPath = SIMPLEFORM_TESTPATH."testcases/";
$testClassLoader = new SimpleForm_ClassLoader($testPath);


// run all tests
$test = new TestSuite('All Tests');
//foreach(glob("$testPath/Test*.php") as $file) $test->addTestFile($file);

// basic DOM tests
$test->addTestFile("$testPath/TestDomHtmlElement.php");
$test->addTestFile("$testPath/TestDomInputElement.php");
$test->addTestFile("$testPath/TestDomTextareaElement.php");
$test->addTestFile("$testPath/TestDomSelectElement.php");
$test->addTestFile("$testPath/TestDomFormModel.php");

// other form components
$test->addTestFile("$testPath/TestFormArrayHelper.php");
$test->addTestFile("$testPath/TestLabelParsing.php");
$test->addTestFile("$testPath/TestRequestInteraction.php");

// system tests
$test->addTestFile("$testPath/TestForm.php");


$test->run(new HtmlReporter());

?>
