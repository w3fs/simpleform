<?php

class TestDomTextareaElement extends UnitTestCase
{
	function setUp()
	{
		$parser = new SimpleForm_Dom_Parser();
		$this->element = new SimpleForm_Dom_Textarea($parser->parseElement(
			'<textarea name="testelement" id="testid">'.
			'this is a value'.
			'</textarea>'));
	}

	function testBasicProperties()
	{
		$this->assertEqual('this is a value', $this->element->getValue());
		$this->assertEqual('testelement', $this->element->getName());
		$this->assertEqual('testid', $this->element->getId());
	}

	function testLoadingHtml()
	{
		$html = 'this <b>is a</b> test';
		$this->element->setValue($html);
		$this->assertEqual($this->element->getValue(), $html);

		$html = '<strong>more text</strong>';
		$this->element->setValue($html);
		$this->assertEqual($this->element->getValue(), $html);

		// finally check we get the entities when we serialize
		$this->assertEqual(
			'<textarea name="testelement" id="testid">'.
			'&lt;strong&gt;more text&lt;/strong&gt;'.
			'</textarea>', $this->element->serialize());
	}
}
?>
