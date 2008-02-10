<?php

class TestDomHtmlElement extends AbstractSimpleFormTestCase
{
	function setUp()
	{
		$domParser = new SimpleForm_Dom_Parser();

		// create a simple input element
		$this->input = new SimpleForm_Dom_HtmlElement($domParser->parseElement(
			'<input id="test" type="input" class="" value="meh"/>'
			));

		// create a label with an input in it
		$this->label = new SimpleForm_Dom_HtmlElement($domParser->parseElement(
			'<label>Input Field:<input id="test" />(optional)</label>'
			));
	}

	function testManipulatingAttributes()
	{
		$this->assertEqual($this->input->getAttributes(),array(
			'id'=>'test','class'=>'','type'=>'input','value'=>'meh'));
		$this->assertEqual($this->input->getAttribute('id'),'test');
		$this->assertEqual($this->input->getAttribute('class'),'');
		$this->assertEqual($this->input->getAttribute('notthere'),NULL);
		$this->input->setAttribute('notthere','test');
		$this->assertEqual($this->input->getAttribute('notthere'),'test');
		$this->assertEqual($this->input->getAttributes(),array(
			'id'=>'test','class'=>'','type'=>'input',
			'value'=>'meh','notthere'=>'test'));
		$this->assertFalse($this->input->hasAttribute('blarg'));
		$this->input->clearAttributes();
		$this->assertEqual($this->input->getAttributes(),array());
	}

	function testAddingClasses()
	{
		$this->input->setAttribute('class',' testing classes   blarg');
		$this->assertEqual($this->input->getClasses(),array('testing',
			'classes','blarg'));
		$this->input->addClass('test');
		$this->assertEqual($this->input->getClasses(),array('testing',
			'classes','blarg','test'));
		$this->assertEqual($this->input->getAttribute('class'),
			' testing classes   blarg test');
		$this->input->addClass('classes'); // already exists
		$this->assertEqual($this->input->getAttribute('class'),
			' testing classes   blarg test');
	}

	function testRemovingClasses()
	{
		$this->input->setAttribute('class',' testing classes   blarg');
		$this->input->removeClass('classes');
		$this->assertEqual($this->input->getClasses(),array('testing','blarg'));
		$this->assertEqual($this->input->getAttribute('class'),
			' testing    blarg');
	}

	function testGettingInnerText()
	{
		$this->assertEqual($this->label->getInnerText(),
			'Input Field:(optional)');
	}

	function testSettingInnerTextToAnElementWithExistingText()
	{
		$this->label->setInnerText('blarg!');
		$this->assertEqual($this->label->getInnerText(),
			'blarg!');
	}

	function testSerializingAnElement()
	{
		$this->assertEqual($this->label->serialize(),
			'<label>Input Field:<input id="test"/>(optional)</label>');
	}
}

?>
