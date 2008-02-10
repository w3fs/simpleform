<?php

class TestForm extends AbstractSimpleFormTestCase
{
	function setUp()
	{
		$_GET = array();
		$_POST = array();
		$_SERVER = array('REQUEST_METHOD'=>'get','REQUEST_URI'=>'');
	}

	/**
	 * Tests that forms without identifiers fail
	 */
	function testEmptyFormsWork()
	{
		$form = new SimpleForm_Form();
		$this->assertEqual('<form id="simpleform"/>',$form->output(false));
	}

	/**
	 * Tests that forms without identifiers fail
	 */
	function testFormsWithNoIdFail()
	{
		$this->expectException();
		$form = new SimpleForm_Form();
		$form->parse('<form></form>');
	}

	/**
	 * Tests that parsing non forms fails
	 */
	function testParsingNonFormsFails()
	{
		$this->expectException();
		$form = new SimpleForm_Form();
		$form->parse('<rubbish></rubbish>');
	}

	/**
	 * Tests that the correct elements are parsed from the single field form
	 */
	function testElementsAreParsed()
	{
		$form = new SimpleForm_Form();
		$form->parse($this->getFormHtml('singleelementform.html'));
		$elements = $form->getAllElements();

		$this->assertEqual(count($elements), 1);
		$this->assertEqual($elements[0]->getTagName(), 'input');

		// test attributes
		$this->assertEqual($elements[0]->getAttributes(), array(
				'id'=>'testelement',
				'name'=>'testelement',
				'type'=>'text',
				'class'=>'text',
				'value'=>'Element Contents'
				));

		$this->assertIsA($elements[0], 'SimpleForm_Input');

		$this->assertEqual($elements[0]->getAttribute('id'), 'testelement');
		$this->assertEqual($elements[0]->getAttribute('name'), 'testelement');
		$this->assertEqual($elements[0]->getAttribute('type'), 'text');
		$this->assertEqual($elements[0]->getAttribute('class'), 'text');
		$this->assertEqual($elements[0]->getAttribute('value'), 'Element Contents');

		// check reflexive conditions
		$this->assertEqual($elements[0]->getAttribute('value'), $elements[0]->getValue());
		$this->assertEqual($elements[0]->getAttribute('id'), $elements[0]->getId());
	}

	function testSimpleRoundTrip()
	{
		$form = new SimpleForm_Form();
		$form->parse($this->getFormHtml('singleelementform.html'));
		$output = $form->output(false);

		$form = new SimpleForm_Form();
		$form->parse($output);
		$elements = $form->getAllElements();

		$this->assertEqual(count($elements), 1);
		$this->assertEqual($elements[0]->getTagName(), 'input');
	}

	function testSimpleModification()
	{
		$form = new SimpleForm_Form();
		$form->parse($this->getFormHtml('singleelementform.html'));
		$elements = $form->getAllElements();

		$this->assertEqual(count($elements), 1);
		$this->assertEqual($elements[0]->getTagName(), 'input');

		// set the value on an element
		$elements[0]->setValue('newvalue');
		$output = $form->output(false);

		// round trip it
		$form = new SimpleForm_Form();
		$form->parse($output);

		$element = $form->getElementById('testelement');

		$this->assertIsA($element, 'SimpleForm_Input');
		$this->assertEqual($element->getValue(), 'newvalue');
		$this->assertEqual($element->getValue(), $element->getAttribute('value'));
	}

	/**
	 * By default, shouldn't set the value of a hidden input elements
	 */
	function testSettingHiddenInputs()
	{
		$form = new SimpleForm_Form();
		$form->parse($this->getFormHtml('hiddenelementform.html'));

		$form->load(array(
			'testfield1' => 'newvalue',
			'testfield2' => 'another new value',
			));

		$hiddenelement = $form->getElementById('testfield1');
		$textelement = $form->getElementById('testfield2');

		// hidden value should still be default
		$this->assertEqual('defaultvalue1', $hiddenelement->getValue());
		$this->assertEqual('another new value', $textelement->getValue());
	}

	/**
	 * Should be able to force the settting of values of hidden input elements
	 */
	function testForcingHiddenInputs()
	{
		$form = new SimpleForm_Form();
		$form->parse($this->getFormHtml('hiddenelementform.html'));

		$form->load(array(
			'testfield1' => 'newvalue',
			'testfield2' => 'second new value',
			), true);
		$hiddenelement = $form->getElementById('testfield1');
		$textelement = $form->getElementById('testfield2');

		// hidden value should be filled
		$this->assertEqual('newvalue', $hiddenelement->getValue());
		$this->assertEqual('second new value', $textelement->getValue());
	}

	/**
	 * Should be able to extract form attributes from the form
	 */
	function testFormAttributes()
	{
		$form = new SimpleForm_Form();
		$form->parse($this->getFormHtml('hiddenelementform.html'));

		$this->assertEqual($form->getMethod(),'post');
		$this->assertEqual($form->getId(),'testform');
		$this->assertEqual($form->getAction(),'someurl');
	}

	/**
	 * Should be able to inject hidden elements into a form
	 */
	function testHiddenFieldInjection()
	{
		$form = new SimpleForm_Form();
		$form->parse($this->getFormHtml('hiddenelementform.html'));

		// inject the hidden
		$form->injectHiddenElement('newhidden','myvalue');
		$elements = $form->getElementsByName('newhidden');

		$this->assertEqual(1, count($elements));
		$this->assertEqual(3, count($form->getAllElements()));
		$this->assertTrue($elements[0]->getName(),'newhidden');
		$this->assertTrue($elements[0]->getValue(),'myvalue');

		$this->assertWantedPattern('/newhidden/',$form->output(false));
	}

	/**
	 * Should inject a hidden field into the form that doesn't show up via the simpleform API
	 */
	function testSubmitMarkerInjection()
	{
		$form = new SimpleForm_Form();
		$form->parse($this->getFormHtml('hiddenelementform.html'));

		$this->assertEqual(2, count($form->getAllElements()));
		$this->assertEqual(0, count($form->getElementsByName('simpleform_submit_marker')));

		$this->assertWantedPattern('/simpleform_submit_marker/',$form->output(false));
	}

	/**
	 * Tests that parsed forms can round trip select element values
	 */
	function testParsedFormsCanRoundTripSelectElementValues()
	{
		$form = new SimpleForm_Form();
		$form->parse($this->getFormHtml('complexform.html'));

		$select = $form->getElementById('myselect');
		$select->setValue('blah');

		$options = $select->getOptions();
		$this->assertTrue(isset($options[1][1]['selected']));

		$this->assertWantedPattern('/selected="selected"/',$select->serialize());
		$this->assertWantedPattern('/selected="selected"/',$form->output(false));
	}

	/**
	 * Tests that parsed forms can round trip checkboxelement values
	 */
	function testParsedFormsCanRoundTripCheckboxValues()
	{
		$form = new SimpleForm_Form();
		$form->parse($this->getFormHtml('checkboxgroupform.html'));

		$checkboxes = $form->getElementsByName('checkbox');

		$this->assertEqual(3, count($checkboxes));
		$checkboxes[1]->setValue('value2');

		$this->assertWantedPattern('/checked="checked"/',$checkboxes[1]->serialize());
	}

	/**
	 * Forms can detect if they are submitted
	 */
	function testFormsCanDetectSubmit()
	{
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_POST = array(
			'simpleform_submit_marker' => 'myform'
			);

		$form = new SimpleForm_Form();
		$form->parse($this->getFormHtml('complexform.html'));
		$this->assertTrue($form->isSubmitted());
		$this->assertFalse($form->isCancelled());
	}

	/**
	 * Forms aren't marked as submitted if the method doesn't match the REQUEST_METHOD
	 */
	function testFormsMatchTheMethodToTheRequest()
	{
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_GET = array(
			'simpleform_submit_marker' => 'myform'
			);

		$form = new SimpleForm_Form();
		$form->parse($this->getFormHtml('complexform.html'));
		$this->assertFalse($form->isSubmitted());
		$this->assertFalse($form->isCancelled());
	}

	/**
	 * Forms don't detect submission if another form submitted the data
	 */
	function testFormsMatchSubmissionToFormId()
	{
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_POST = array('test' => 'value');

		$form = new SimpleForm_Form();
		$form->parse($this->getFormHtml('complexform.html'));
		$this->assertTrue($form->getMethod(),strtolower($_SERVER['REQUEST_METHOD']));
		$this->assertFalse($form->isSubmitted());
		$this->assertFalse($form->isCancelled());
	}

	/**
	 * Forms can detect if they are cancelled
	 */
	function testFormsCanDetectCancel()
	{
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_POST = array(
			'simpleform_submit_marker' => 'myform',
			'cancel'=>'Cancel'
			);

		$form = new SimpleForm_Form();
		$form->parse($this->getFormHtml('complexform.html'));
		$this->assertTrue($form->isCancelled());
		$this->assertTrue($form->isSubmitted());
	}

	/**
	 * Forms should automatically load the request array if the form was submitted
	 */
	function testFormsAutoloadRequest()
	{
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_POST = array(
			'testelement'=>'New Content',
			'myselect'=>'blah',
			'simpleform_submit_marker' => 'myform'
			);

		$form = new SimpleForm_Form();
		$form->parse($this->getFormHtml('complexform.html'));
		$this->assertTrue($form->isSubmitted());

		$getValues = $form->getValues();

		$this->assertEqual('New Content', $getValues['testelement']);
		$this->assertEqual('blah', $getValues['myselect']);
	}

	/**
	 * Forms should infer unchecked radio and checkboxes when a full request is loaded
	 */
	function testFormsFillEmpties()
	{
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_POST = array(
			'simpleform_submit_marker' => 'myform'
			);

		$form = new SimpleForm_Form();
		$form->setAutoLoad(false);
		$form->parse($this->getFormHtml('checkboxgroupform.html'));

		$this->assertTrue($form->isSubmitted());
		$this->assertEqual($form->getRequestParams(), $_POST);

		$getValues = $form->getValues();
		$this->assertEqual('value1', $getValues['checkbox']);
		$this->assertEqual('value1', $getValues['radio']);

		// because checkbox and radio weren't in the request, they get NULLed
		$form->loadRequest();

		$getValues = $form->getValues();
		$this->assertEqual(NULL, $getValues['checkbox']);
		$this->assertEqual(NULL, $getValues['radio']);
	}

	// ---------------------------------------------------------
	// GetValues tests

	/**
	 * Should show all the elements with a correct value
	 */
	function testComplexFormGetValues()
	{
		$form = new SimpleForm_Form();
		$form->parse($this->getFormHtml('complexform.html'));

		$this->assertEqual(5, count($form->getAllElements()));
		$this->assertEqual(3, count($form->getAllLabels()));

		$this->assertEqual(array(
			'testelement'=>'Element Contents',
			'myselect'=>'First Option',
			'editor'=>'Some Initial Text',
			'submit'=>'Submit',
			'cancel'=>'Cancel',
			), $form->getValues());
	}

	function testLongFormParsing()
	{
		$form = new SimpleForm_Form();
		$form->parse($this->getFormHtml('longform.html'));
	}

	/**
	 * Should be able to get a simple array of element names and array values
	 */
	function testFormGetValues()
	{
		$form = new SimpleForm_Form();
		$form->parse($this->getFormHtml('hiddenelementform.html'));

		$this->assertEqual(array('testfield1'=>'defaultvalue1','testfield2'=>'defaultvalue2'),
			$form->getValues());
	}

	function testExpandedGetValues()
	{
		$form = new SimpleForm_Form();
		$form->parse($this->getFormHtml('complextextarrayform.html'));
		$array = $form->getExpandedValues();

		$this->assertEqual($array['textbox']['e']['meh'],'blah');
		$this->assertEqual($array['textbox']['f']['g'][1],99);
		$this->assertEqual($array['textbox']['q'],array());
		$this->assertEqual($array['textbox']['d'],array());

		$this->assertNull($array['textbox']['a']);
		$this->assertNull($array['textbox']['b']);
		$this->assertNull($array['textbox']['c']);
		$this->assertNull($array['textbox']['f'][2]);
		$this->assertNull($array['textbox']['f'][3]);
	}

	// ---------------------------------------------------------
	// element lookup tests

	/**
	 * Should be able to get a single elements based on its id
	 */
	function testGetElementById()
	{
		$form = new SimpleForm_Form();
		$form->parse($this->getFormHtml('hiddenelementform.html'));

		$this->assertIsA($form->getElementById('testfield1'), 'SimpleForm_FormElement');
		$this->assertIsA($form->getElementById('testfield2'), 'SimpleForm_FormElement');

		$this->expectException();
		$form->getElementById('missing');

		$r1 = $form->getElementById('testfield1');
		$r2 = $form->getElementById('testfield1');

		// set subsequent fetches gets the same reference
		$this->assertReference($r1,$r2);
	}

	/**
	 * Should be able to get elements based on the form element name
	 */
	function testGetElementsByName()
	{
		$form = new SimpleForm_Form();
		$form->parse($this->getFormHtml('hiddenelementform.html'));

		$this->assertEqual(1, count($form->getElementsByName('testfield1')));
		$this->assertEqual(1, count($form->getElementsByName('testfield2')));
		$this->assertEqual(0, count($form->getElementsByName('missing')));

		$r1 = $form->getElementsByName('testfield1');
		$r2 = $form->getElementsByName('testfield1');

		// set subsequent fetches gets the same reference
		$this->assertReference($r1[0],$r2[0]);
	}

	/**
	 * Should be able to get elements based on the tag name
	 */
	function testGetElementsByTag()
	{
		$form = new SimpleForm_Form();
		$form->parse($this->getFormHtml('hiddenelementform.html'));

		$this->assertEqual(2, count($form->getElementsByTagName('input')));
		$this->assertEqual(0, count($form->getElementsByTagName('unknown')));

		$r1 = $form->getElementsByTagName('input');
		$r2 = $form->getElementsByTagName('input');

		// set subsequent fetches gets the same reference
		$this->assertReference($r1[0],$r2[0]);
		$this->assertReference($r1[1],$r2[1]);
	}

	/**
	 * Should handle getting non-existent elements
	 */
	function testGettingNonExistentElements()
	{
		$form = new SimpleForm_Form();
		$form->parse($this->getFormHtml('hiddenelementform.html'));

		$this->expectException();
		$form->getElementById('nonexistentfield');
		$elements = $form->getElementsByName('nonexistentfield');
		$this->assertEqual(array(), $elements);
	}

	/**
	 * Checkbox arrays should be able to be found
	 */
	function testDetectingCheckboxArrays()
	{
		$form = new SimpleForm_Form();
		$form->parse($this->getFormHtml('checkboxarrayform.html'));

		$checkboxes = $form->getElementsByName('checkbox[]');
		$this->assertEqual(count($checkboxes), 3);
	}

	// ---------------------------------------------------------
	// load tests

	/**
	 * Forms should be able to load an array of key values
	 */
	function testLoadingValues()
	{
		$form = new SimpleForm_Form();
		$form->parse($this->getFormHtml('singleelementform.html'));
		$testelement = $form->getElementById('testelement');

		$this->assertEqual('Element Contents',$testelement->getValue());

		$form->load(array(
			'testelement' => 'newvalue',
			));

		$changedtestelement = $form->getElementById('testelement');
		$this->assertReference($testelement, $changedtestelement);
		$this->assertEqual('newvalue',$testelement->getValue());
	}

	/**
	 * Loading a value into a form with checkboxes should set the correct one as checked
	 */
	function testLoadSetsCheckboxes()
	{
		$form = new SimpleForm_Form();
		$form->parse($this->getFormHtml('checkboxgroupform.html'));

		$form->load(array('checkbox'=>'value2'));

		$checkboxes = $form->getElementsByName('checkbox');
		$this->assertWantedPattern('/checked="checked"/',$checkboxes[1]->serialize());
	}

	/**
	 * Loading a value into a form with checkboxes should set the correct one as checked
	 */
	function testLoadSetsCheckboxArrays()
	{
		$form = new SimpleForm_Form();
		$form->parse($this->getFormHtml('checkboxarrayform.html'));
		$checkboxes = $form->getElementsByName('checkbox[]');
		$this->assertWantedPattern('/checked="checked"/',$checkboxes[0]->serialize());

		$form->load(array('checkbox[]'=>array('value2')));

		//Spf::dump($form->getValues());

		$this->assertNoPattern('/checked="checked"/',$checkboxes[0]->serialize());
		$this->assertWantedPattern('/checked="checked"/',$checkboxes[1]->serialize());
	}

	/**
	 * Loading values into form with element[blah][] style elements works
	 */
	function testLoadSetsComplexArrayValues()
	{
		$form = new SimpleForm_Form();
		$form->parse($this->getFormHtml('complextextarrayform.html'));

		$data = array(
			'textbox'=>array(
				'a'=>'testa',
				'b'=>'testb',
				'c'=>'testc',
				'd'=>array('text1','text2','text3'),
				'f'=>array(
					1=>'test',
					'g'=>array('val1','val2')
					)
				),
			);

		$form->load($data);
		$array = $form->getValues();

		$this->assertEqual($array['textbox[a]'],'testa');
		$this->assertEqual($array['textbox[b]'],'testb');
		$this->assertEqual($array['textbox[c]'],'testc');
		$this->assertEqual($array['textbox[d]'],array('text1','text2','text3'));
		$this->assertEqual($array['textbox[e][meh]'],'blah');
		$this->assertNull($array['textbox[f][2]']);
		$this->assertNull($array['textbox[f][3]']);
		$this->assertEqual($array['textbox[f][g]'],array('val1','val2'));
		$this->assertEqual($array['textbox[q]'],array());
	}

	/**
	 * Loading a request into a form with checkboxes should set the correct one as checked
	 */
	function testLoadRequestSetsCheckboxArrays()
	{
		$form = new SimpleForm_Form();
		$form->parse($this->getFormHtml('checkboxarrayform.html'));
		$checkboxes = $form->getElementsByName('checkbox[]');
		$this->assertWantedPattern('/checked="checked"/',$checkboxes[0]->serialize());

		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_POST = array('checkbox'=>array('value2','value3'));
		$form->loadRequest();

		$this->assertEqual(array('checkbox'=>array('value2','value3')),
			$form->getValues());

		$this->assertNoPattern('/checked="checked"/',$checkboxes[0]->serialize());
		$this->assertWantedPattern('/checked="checked"/',$checkboxes[1]->serialize());
		$this->assertWantedPattern('/checked="checked"/',$checkboxes[2]->serialize());
	}

	function testLoadRequestDoesntOverwriteNumericKeyArrays()
	{
		$form = new SimpleForm_Form();
		$form->parse($this->getFormHtml('numberedtextarrayform.html'));

		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_POST = array('textbox'=>array(5=>'test data'));
		$form->loadRequest();

		$array = $form->getValues();
		$this->assertEqual($array['textbox[5]'],'test data');
	}
}
?>

