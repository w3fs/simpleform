<?php

class TestFormArrayHelper extends AbstractSimpleFormTestCase
{
	function testExpand()
	{
		$from = array(
			'test[a]' => 'vala',
			'test[b]' => 'valb',
			'test[c]' => 'valc',
			'test[d][0]' => 'd0',
			'test[d][1]' => 'd1',
			);

		$this->assertEqual(
			SimpleForm_FormArrayHelper::expand($from),
			array(
				'test'=>array(
					'a'=>'vala',
					'b'=>'valb',
					'c'=>'valc',
					'd'=>array('d0','d1'),
						)
				)
			);
	}

	function testCollapse()
	{
		$from = array(
			'test'=>array(
				'a'=>'vala',
				'b'=>'valb',
				'c'=>'valc',
				'd'=>array(
					'd0',
					'd1'
					),
				),
			'top'=>'blah'
			);

		$this->assertEqual(
			SimpleForm_FormArrayHelper::collapse($from),
			array(
				'test[a]' => 'vala',
				'test[b]' => 'valb',
				'test[c]' => 'valc',
				'test[d][0]' => 'd0',
				'test[d][1]' => 'd1',
				'top' => 'blah',
				));
	}

	function testComplexCollapse()
	{
		$from = array(
			'textbox'=>array(
				'd'=>array('text1'),
				'f'=>array(
					'g'=>array('val1','val2')
					)
				));

		$this->assertEqual(
			SimpleForm_FormArrayHelper::collapse($from),
			array(
				'textbox[d][0]' => 'text1',
				'textbox[f][g][0]' => 'val1',
				'textbox[f][g][1]' => 'val2',
				));
	}

	function testCollapseWithNumericKeys()
	{
		$from = array(
			'prize'=>
				array(
					5=>'120',
					22=>'120'
					)
				);

		$this->assertEqual(
			SimpleForm_FormArrayHelper::collapse($from),
			array(
				'prize[5]' => '120',
				'prize[22]' => '120',
				));
	}

}
?>

