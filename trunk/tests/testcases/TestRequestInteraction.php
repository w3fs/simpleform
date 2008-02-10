<?php

Mock::generate('SimpleForm_FormElement','SimpleForm_MockFormElement');

class TestRequestInteraction extends AbstractSimpleFormTestCase
{
	function setUp()
	{
		$_GET = array();
		$_POST = array();
		$_SERVER = array('REQUEST_METHOD'=>'get','REQUEST_URI'=>'');
	}

	function testCheckingTheFormIsSubmitted()
	{
		$form = new SimpleForm_Form();
		$form->parse($this->getFormHtml('inputform.html'));

		$this->assertFalse($form->isSubmitted());

		// fake the submission of the form
		$_GET[SimpleForm_Form::SUBMIT_MARKER] = 'inputform';

		$this->assertEqual('inputform', $form->getId());
		$this->assertTrue($form->isSubmitted());
	}

	function testCheckingTheFormIsCancelled()
	{
		$form = new SimpleForm_Form();
		$form->parse($this->getFormHtml('inputform.html'));
		$this->assertFalse($form->isSubmitted());
		$this->assertFalse($form->isCancelled());

		// fake the submission of the form
		$_GET[SimpleForm_Form::SUBMIT_MARKER] = 'inputform';

		// submission does not equal cancelled
		$this->assertTrue($form->isSubmitted());
		$this->assertFalse($form->isCancelled());

		// but it should after cancel has a value
		$_GET[SimpleForm_Form::CANCEL_MARKER] = 'Cancel';
		$this->assertTrue($form->isSubmitted());
		$this->assertTrue($form->isCancelled());
	}

	function testValuesAreAutoloaded()
	{
		// fake the submission of the form
		$_GET[SimpleForm_Form::SUBMIT_MARKER] = 'inputform';
		$_GET['firstname'] = 'Lachlan';
		$_GET['lastname'] = 'Donald';

		$form = new SimpleForm_Form();
		$form->parse($this->getFormHtml('inputform.html'));

		$this->assertEqual(array(
			'firstname'=>'Lachlan',
			'lastname'=>'Donald',
			), $form->getValues());
	}
}

?>
