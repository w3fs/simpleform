<?php

class TestLabelParsing extends AbstractSimpleFormTestCase
{
	/**
	 * Tests that labels are parsed correctly
	 */
	function testLabelsAreParsed()
	{
		$form =
		'<form id="test">'.
		'   <div>'.
		'      <label for="testelement">Test Element</label>'.
		'      <input id="testelement" name="testelement" type="text" '.
		'             class="text" value="Element Contents" />'.
		'   </div>'.
		'</form>';

		$model = new SimpleForm_Dom_FormModel($form);
		$labels = $model->getAllLabels();

		$this->assertEqual(count($labels),1);
		$this->assertIsA($labels[0],'SimpleForm_Label');
		$this->assertEqual($labels[0]->getText(), 'Test Element');
	}

	/**
	 * Tests that explicitly associated labels work
	 */
	function testExplicitLabelAssociations()
	{
		$form =
		'<form id="test">'.
		'   <div>'.
		'      <label for="testelement">Test Element</label>'.
		'      <input id="testelement" name="testelement" type="text" '.
		'             class="text" value="Element Contents" />'.
		'   </div>'.
		'</form>';

		$model = new SimpleForm_Dom_FormModel($form);
		$labels = $model->getAllLabels();

		$this->assertEqual(count($labels),1);
		$this->assertIsA($labels[0],'SimpleForm_Label');
		$this->assertEqual($labels[0]->getText(), 'Test Element');
		$this->assertEqual($labels[0]->getAssociatedElementId(), 'testelement');
	}

	/**
	 * Tests that implicitly associated labels work
	 */
	function testImplicitLabelAssociations()
	{
		$form =
		'<form id="test">'.
		'   <div>'.
		'      <label>Test Element'.
		'      <input id="testelement" name="testelement" type="text" '.
		'             class="text" value="Element Contents" />'.
		'      </label>'.
		'   </div>'.
		'</form>';

		$model = new SimpleForm_Dom_FormModel($form);
		$labels = $model->getAllLabels();

		$this->assertEqual(count($labels),1);
		$this->assertIsA($labels[0],'SimpleForm_Label');
		$this->assertEqual($labels[0]->getText(), 'Test Element');
		$this->assertEqual($labels[0]->getAssociatedElementId(), 'testelement');

		// get the element
		$element = $model->getElementById($labels[0]->getAssociatedElementId());
		$this->assertIsA($element,'SimpleForm_Input');

		// check that elements can figure out which labels are associated with them
		$assocLabels = $element->getAssociatedLabels();
		$this->assertEqual(count($assocLabels), 1);
		$this->assertEqual($assocLabels[0]->getText(), 'Test Element');

		// check the labels are a reference
		$this->assertReference($assocLabels[0], $labels[0]);
	}

	/**
	 * Tests that elements can fetch associated labels correctly
	 */
	function testElementsCanGetLabels()
	{
		$form =
		'<form id="test">'.
		'   <div>'.
		'      <label for="testelement">Test Element</label>'.
		'      <input id="testelement" name="testelement" type="text" '.
		'             class="text" value="Element Contents" />'.
		'   </div>'.
		'</form>';

		$model = new SimpleForm_Dom_FormModel($form);
		$element = $model->getElementById('testelement');

		$this->assertIsA($element,'SimpleForm_Input');

		$labels = $element->getAssociatedLabels();
		$this->assertEqual(count($labels), 1);
		$this->assertEqual($labels[0]->getText(), 'Test Element');
	}

	/**
	 * Tests that implicit labels can still be modified, along with the element in them
	 */
	function testImplicitLabelsCanBeModified()
	{
		$form =
		'<form id="test">'.
		'   <div>'.
		'      <label>Test Element'.
		'      <input id="testelement" name="testelement" type="text" '.
		'             class="text" value="Element Contents" />'.
		'      </label>'.
		'   </div>'.
		'</form>';

		$model = new SimpleForm_Dom_FormModel($form);
		$element = $model->getElementById('testelement');
		$element->setValue('i love cookies');

		$labels = $element->getAssociatedLabels();
		$label = $labels[0];
		$label->setText('A new label!');

		// round trip the form
		$model->parse($model->serialize());

		$element = $model->getElementById('testelement');
		$labels = $element->getAssociatedLabels();
		$label = $labels[0];

		$this->assertEqual($element->getValue(),'i love cookies'); // mmmm cookies
		$this->assertEqual($label->getText(),'A new label!');
	}

	/**
	 * Tests that label text can be extracted, even from complex labels
	 */
	function testSimpleLabelSummary()
	{
		$form =
		'<form id="test">'.
		'   <fieldset>'.
		'      <label>Cookies are awesome'.
		'      <input id="testelement" name="testelement" type="text" '.
		'             class="text" value="Element Contents" />'.
		'      </label>'.
		'   </fieldset>'.
		'</form>';

		$model = new SimpleForm_Dom_FormModel($form);
		$labels = $model->getLabelsByElementId('testelement');

		$this->assertEqual($labels[0]->getLabelSummary(),'Cookies are awesome');
		$this->assertEqual($labels[0]->getText(),'Cookies are awesome');
	}

	/**
	 * Tests that labels that have elements within them can still have a summary
	 * extracted
	 */
	function testLabelsWithElementsBeneathLabelSummary()
	{
		$form =
		'<form id="test">'.
		'   <fieldset>'.
		'      <label for="testelement"><strong>Cookies are awesome</strong>'.
		'<span>Ignore Me</span></label>'.
		'      <input id="testelement" name="testelement" type="text" '.
		'             class="text" value="Element Contents" />'.
		'   </fieldset>'.
		'</form>';

		$model = new SimpleForm_Dom_FormModel($form);
		$labels = $model->getLabelsByElementId('testelement');

		$this->assertEqual($labels[0]->getLabelSummary(),'Cookies are awesome'); // mmmm cookies
		$this->assertEqual($labels[0]->getText(),'');
	}
}
?>

