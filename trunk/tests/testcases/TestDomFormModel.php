<?php

class TestDomFormModel extends AbstractSimpleFormTestCase
{
	function setUp()
	{
		$this->model = new SimpleForm_Dom_FormModel();
		$this->model->parse($this->getFormHtml('complexform.html'));
	}

	function testFindingElementById()
	{
		$this->assertEqual('testelement',
			$this->model->getElementById('testelement')->getId());
		$this->assertEqual('myselect',
			$this->model->getElementById('myselect')->getId());
	}

	function testFindByIdThrowsExceptionWhenItMisses()
	{
		try
		{
			$this->model->getElementById('meh');
			$this->fail("looking for a non-existant element meh should fail");
		}
		catch(SimpleForm_Exception $e)
		{
			$this->assertTrue(true);
		}
	}

	function testFindingElementsByName()
	{
		$els = $this->model->getElementsByName(array('testelement','myselect'));
		$notEls = $this->model->getElementsByName('notthere');

		$this->assertEqual(count($els), 2);
		$this->assertEqual('testelement',$els[0]->getId());
		$this->assertEqual('myselect',$els[1]->getId());
		$this->assertEqual($notEls,array());
	}

	function testFindingElementsByTagName()
	{
		$els = $this->model->getElementsByTagName('input');

		$this->assertEqual(count($els), 3);
		$this->assertEqual('testelement',$els[0]->getId());
		$this->assertEqual('submit',$els[1]->getId());
		$this->assertEqual('cancel',$els[2]->getId());
	}

	function testGettingRootElement()
	{
		$element = $this->model->getRootElement();
		$this->assertEqual('myform',$element->getAttribute('id'));
		$this->assertEqual('post',$element->getAttribute('method'));
	}

	function testInjectingAHiddenElement()
	{
		$this->model->injectHiddenElement('test','blarg');

		$elements = $this->model->getElementsByName('test');
		$this->assertEqual('blarg',$elements[0]->getValue());

		// check it counts as an input
		$this->assertEqual(4, count($this->model->getElementsByTagName('input')));
	}

	function testSerializingAHiddenElement()
	{
		$this->model->injectHiddenElement('test','blarg');
		$elements = $this->model->getElementsByName('test');
		$this->assertEqual('<input type="hidden" name="test" value="blarg"/>',
			$elements[0]->serialize());
	}

	function testMultipleLookupsResultInSameObject()
	{
		$e1 = $this->model->getElementById('testelement');
		$byTagName1 = $this->model->getElementsByTagName('input');
		$byName1 = $this->model->getElementsByName('testelement');
		$all1 =  $this->model->getAllElements();
		$e2 = $this->model->getElementById('testelement');
		$byTagName2 = $this->model->getElementsByTagName('input');
		$byName2 = $this->model->getElementsByName('testelement');
		$all2 =  $this->model->getAllElements();

		$this->assertReference($e1, $e2);
		$this->assertReference($e1, $byTagName1[0]);
		$this->assertReference($e1, $byName1[0]);
		$this->assertReference($e1, $all1[0]);
		$this->assertReference($e2, $byTagName2[0]);
		$this->assertReference($e2, $byName2[0]);
		$this->assertReference($e2, $all2[0]);
	}

	function testInjectHtmlChunk()
	{
		// TODO: implement this
	}
}

?>
