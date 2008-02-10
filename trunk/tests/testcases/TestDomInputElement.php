<?php

class TestDomInputElement extends AbstractSimpleFormTestCase
{
	/**
	 * Creates an input element
	 */
	function _createInputElement($html='<input/>')
	{
		$parser = new SimpleForm_Dom_Parser();
		return new SimpleForm_Dom_Input($parser->parseElement($html));
	}

	function testElementCreation()
	{
		$element = $this->_createInputElement();
		$this->assertTrue(is_object($element));
	}

	/**
	 * Should be able to serialize the element
	 */
	function testBasicSerialization()
	{
		$element = $this->_createInputElement();
		$element->setAttribute('value','test');
		$element->setAttribute('id','myid');
		$element->setType('checkbox');
		$element->setValue('test');

		$this->assertEqual(
			'<input value="test" id="myid" type="checkbox" checked="checked"/>',
			$element->serialize());
	}

	function testInputElementAsCheckbox()
	{
		$element = $this->_createInputElement('<input value="test"/>');

		// check that the element is a text input by default
		$this->assertEqual('text',$element->getType());

		$element->setType('checkbox');
		$element->setValue('test');

		$this->assertEqual('checkbox',$element->getType());
		$this->assertTrue($element->hasAttribute('checked'));
		$this->assertEqual('checked', $element->getAttribute('checked'));
		$this->assertEqual('test',$element->getValue());
	}

	function testInputElementAsRadio()
	{
		$element = $this->_createInputElement('<input value="test"/>');

		$element->setType('radio');
		$element->setValue('test');

		$this->assertEqual('radio',$element->getType());
		$this->assertTrue($element->hasAttribute('checked'));
		$this->assertEqual('checked', $element->getAttribute('checked'));

		$this->assertEqual('test',$element->getValue());
	}
}
?>

