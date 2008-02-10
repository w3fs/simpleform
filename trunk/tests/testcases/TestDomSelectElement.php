<?php

class TestDomSelectElement extends UnitTestCase
{
	function _createSelectElement($html='<select/>')
	{
		$parser = new SimpleForm_Dom_Parser();
		return new SimpleForm_Dom_Select($parser->parseElement($html));
	}

	function _createSimpleSelect()
	{
		return $this->_createSelectElement(
			'<select id="myselect" name="myselect">'.
			'<option>Option 1</option>'.
			'<option value="My Value">Option 2</option>'.
			'<select>');
	}

	function _createSelectWithOptgroups()
	{
		return $this->_createSelectElement(
			'<select name="myselect"><option>Opt1</option>'.
			'<optgroup label="test"><option>Opt2</option>'.
			'<option test="test">Opt3</option></optgroup>'.
			'</select>');
	}

	function _createEmptySelect()
	{
		return $this->_createSelectElement(
			'<select id="test" name="test"></select>');
	}

	/**
	 * Should be able to serialize top level options
	 */
	function testOptionSerialization()
	{
		$select = $this->_createEmptySelect();
		$this->assertEqual(array(), $select->getOptions());
		$select->addOption('value1', array('value'=>'test'));

		$this->assertEqual('<select id="test" name="test">'.
			'<option value="test">value1</option></select>',
			$select->serialize());
	}

	/**
	 * Should be able to add options
	 */
	function testAddingOptions()
	{
		$select = $this->_createEmptySelect();
		$this->assertEqual(array(), $select->getOptions());
		$select->addOption('value1', array());
		$select->addOption('value2', array());

		$this->assertEqual(array(
			array('value1',array(),false),
			array('value2',array(),false),
			), $select->getOptions());
	}

	/**
	 * Should be able to add OPTGROUP elements
	 */
	function testOptGroups()
	{
		$select = $this->_createEmptySelect();
		$this->assertEqual(array(), $select->getOptions());

		$select->addOptGroup('group 1');
		$select->addOptGroup('group 2');

		$select->addOption('value1', array(), 'group 2');
		$select->addOption('value2', array(), 'group 1');

		$this->assertEqual(array(
			array('value1',array(),'group 2'),
			array('value2',array(),'group 1'),
			), $select->getOptions());

		$this->assertEqual('<select id="test" name="test">'.
			'<optgroup label="group 2"><option>value1</option></optgroup>'.
			'<optgroup label="group 1"><option>value2</option></optgroup></select>',
			$select->serialize());
	}

	/**
	 * Setting values into a select should make the corresponding options have
	 * an attribute of checked=checked
	 */
	function testSettingValuesSetsCheckedOnOptions()
	{
		$select = $this->_createEmptySelect();
		$this->assertEqual(array(), $select->getOptions());
		$select->addOption('value1', array());
		$select->addOption('blah', array('value'=>'value2'));

		$this->assertEqual(array(
			array('value1',array(),false),
			array('blah',array('value'=>'value2'),false),
			), $select->getOptions());

		// set the value on value1
		$select->setValue('value1');

		$this->assertEqual(array(
			array('value1',array('selected'=>'selected'),false),
			array('blah',array('value'=>'value2'),false),
			), $select->getOptions());

		// set the value on value2
		$select->setValue('value2');

		$this->assertEqual(array(
			array('value1',array(),false),
			array('blah',array('selected'=>'selected','value'=>'value2'),false),
			), $select->getOptions());
	}

	/**
	 * Setting an array of values into a select should make all corresponding options have an attribute
	 * of checked=checked
	 */
	function testMultipleValuesSetsCheckedOnOptions()
	{
		$select = $this->_createEmptySelect();
		$this->assertEqual(array(), $select->getOptions());
		$select->setAttribute('multiple','multiple');
		$select->addOption('value1', array());
		$select->addOption('value2', array());
		$select->addOption('value3', array());

		$select->setValue(array('value1','value2'));

		$this->assertEqual(array(
			array('value1',array('selected'=>'selected'),false),
			array('value2',array('selected'=>'selected'),false),
			array('value3',array(),false),
			), $select->getOptions());
	}

	/**
	 * The value of a select element is equal to the value of the checked options
	 */
	function testSelectValueEqualsCheckedOptions()
	{
		$select = $this->_createEmptySelect();
		$this->assertEqual(array(), $select->getOptions());
		$select->addOption('value1', array('checked'=>'checked'));
		$select->addOption('value2', array());
		$select->addOption('value3', array());

		$this->assertEqual('value1', $select->getValue());
	}

	/**
	 * Setting multiple values fails unless the multiple attribute is set
	 */
	function testSettingMultipleValuesFailsWithoutMultipleAttribute()
	{
		$select = $this->_createEmptySelect();
		$this->assertEqual(array(), $select->getOptions());
		$select->addOption('value1', array());
		$select->addOption('value2', array());
		$select->addOption('value3', array());

		$this->expectException();
		$select->setValue(array('value1','value2'));
	}

	/**
	 * If a select element has the multiple attribute, it can return an array of checked values
	 */
	function testSelectValueEqualsMultipleCheckedOptions()
	{
		$select = $this->_createEmptySelect();
		$this->assertEqual(array(), $select->getOptions());
		$select->setAttribute('multiple','multiple');
		$select->addOption('value1', array('selected'=>'selected'));
		$select->addOption('value2', array('selected'=>'selected'));
		$select->addOption('value3', array());

		$this->assertEqual(array('value1','value2'), $select->getValue());
	}

	/**
	 * If nothing is checked, the value of a select is the first element
	 */
	function testDefaultSelectionWithNoChecked()
	{
		$select = $this->_createEmptySelect();
		$this->assertEqual(array(), $select->getOptions());
		$select->addOption('value1', array());
		$select->addOption('value2', array());
		$select->addOption('value3', array());

		$this->assertEqual('value1', $select->getValue());
	}

	function testOptionElementParsing()
	{
		$select = $this->_createSimpleSelect();

		$this->assertEqual(array(
			array('Option 1',array(),false),
			array('Option 2',array('value'=>'My Value'),false),
			), $select->getOptions());
	}

	function testOptionElementParsingWithOptGroups()
	{
		$select = $this->_createSelectWithOptgroups();

		$this->assertEqual(array(
			array('Opt1',array(),false),
			array('Opt2',array(),'test'),
			array('Opt3',array('test'=>'test'),'test'),
			), $select->getOptions());
	}

	function testParsedOptGroupsCanHaveOptionsAdded()
	{
		$select = $this->_createSelectWithOptgroups();
		$select->addOption('blah',array(),'test');

		$options = $select->getOptions();

		$this->assertEqual(count($options), 4);
		$this->assertEqual($options[3],array('blah',array(),'test'));

		$select->addOptGroup('opt2');
		$select->addOption('blah again',array(),'opt2');

		$options = $select->getOptions();

		$this->assertEqual(count($options), 5);
		$this->assertEqual($options[4],array('blah again',array(),'opt2'));
	}

	function testEmptySelectReturnsEmptyArray()
	{
		$select = $this->_createEmptySelect();
		$this->assertEqual(array(), $select->getOptions());
	}

	function testClearingOptions()
	{
		$select = $this->_createSimpleSelect();
		$select->clearOptions();

		$this->assertEqual(array(), $select->getOptions());
		$this->assertEqual('<select id="myselect" name="myselect"></select>',
			$select->serialize());

	}
}
?>
