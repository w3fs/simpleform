<?php

abstract class AbstractSimpleFormTestCase extends UnitTestCase
{
	function __construct()
	{
		parent::__construct();
	}

	function getFormHtml($filename)
	{
		return file_get_contents(SIMPLEFORM_TESTFORMS.$filename);
	}

	function assertHasElementId(SimpleForm_Form $form, $id, $message=NULL)
	{
		if(is_null($message)) $message="form has no element with an id of $id";

		try
		{
			$element = $form->getElementById($id);
			$this->assertIsA($element, 'SimpleForm_Element');
		}
		catch(SimpleForm_Exception $e)
		{
			$this->fail($message);
		}
	}
}

?>
