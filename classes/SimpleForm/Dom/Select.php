<?php

class SimpleForm_Dom_Select
	extends SimpleForm_Dom_AbstractFormElement
{
	private $_optgroups=array();
	private $_options=array();

	public function __construct(DOMElement $element)
	{
		parent::__construct($element);

		$this->_options = array();
		$this->_optgroups = array();

		// the dom element to process
		$element = $this->getDomElement();

		// process all options
		foreach($element->getElementsByTagName('option') as $opt)
		{
			// process items in optgroups differently
			if($optgroup = $this->_getOptgroupLabel($opt))
			{
				$this->_optgroups[$optgroup] = $opt->parentNode;
			}

			$this->_options[] = $opt;
		}
	}

	/* (non-phpdoc)
	 * @see SimpleForm_FormElement::isReadOnly
	 */
	public function isReadOnly()
	{
		return false;
	}

	/* (non-phpdoc)
	 * @see SimpleForm_FormElement::clear
	 */
	public function clear()
	{
		$this->setValue(array());
	}

	/* (non-phpdoc)
	 * @see SimpleForm_FormElement::getValue
	 */
	function getValue()
	{
		$values = array();

		// find selected options
		foreach($this->_options as $option)
		{
			if($option->hasAttribute('selected') &&
				$option->getAttribute('selected') == 'selected')
			{
				$values[] = $this->_getOptionValue($option);
			}
		}

		// if no options are selected, return the first option
		if(count($values)==0)
		{
			if(count($this->_options))
			{
				return $this->_getOptionValue($this->_options[0]);
			}
		}
		else
		{
			return (count($values)==1) ? $values[0] : $values;
		}
	}

	/* (non-phpdoc)
	 * @see SimpleForm_FormElement::setValue
	 */
	public function setValue($value)
	{
		if(!is_array($value)) $value = array($value);

		// fail if there are multiple values but the multiple setting isn't selected
		if(count($value)>1 && $this->getAttribute('multiple') != 'multiple')
		{
			throw new SimpleForm_Exception(
				'A select can only have multiple values set into it if '.
				'the multiple attribute exists');
		}

		// synchronize the selected attributes
		foreach($this->_options as $option)
		{
			if(in_array($this->_getOptionValue($option), $value))
			{
				$option->setAttribute('selected','selected');
			}
			else if($option->hasAttribute('selected'))
			{
				$option->removeAttribute('selected');
			}
		}
	}

	/**
	 * Adds an OPTION to the select
	 */
	public function addOption($value, $attributes=array(), $optgroup=false)
	{
		$document = $this->getDomDocument();
		$option = $document->createElement('option');
		$option->appendChild($document->createTextNode($value));

		// populate attributes
		foreach($attributes as $key=>$value)
		{
			$option->setAttribute($key,$value);
		}

		// handle optgroups
		if($optgroup !== false)
		{
			if(!isset($this->_optgroups[$optgroup]))
			{
				throw new SimpleForm_Exception(
					"No optgroup named '$optgroup' has been created");
			}

			// if the optgroup has no children, it needs to be appended to the
			// select element as this is the first element
			if(count($this->_optgroups[$optgroup]->hasChildNodes()))
			{
				$this->getDomElement()->appendChild($this->_optgroups[$optgroup]);
			}

			$this->_optgroups[$optgroup]->appendChild($option);
		}
		else
		{
			$this->getDomElement()->appendChild($option);
		}

		// finally add to the options array
		$this->_options[] = $option;
	}

	/**
	 * Adds an OPTGROUP to the select, which later can have options added to it
	 *
	 * @param string $label the label to associate with the optgroup
	 * @param boolean $disabled whether the optgroup is disabled, false by default
	 */
	public function addOptGroup($label, $disabled=false)
	{
		$document = $this->getDomDocument();
		$optgroup = $document->createElement('optgroup');

		$optgroup->setAttribute('label', $label);
		if($disabled) $optgroup->setAttribute('disabled', 'disabled');

		if(isset($this->_optgroups[$label]))
		{
			throw new SimpleForm_Exception(
				"An optgroup with the label '$label' already exists");
		}

		$this->_optgroups[$optgroup->getAttribute('label')] = $optgroup;
	}

	/**
	 * Removes all the options and optgroups
	 */
	public function clearOptions()
	{
		$this->_options = array();
		$this->_optgroups = array();

		// apparently this removes all children, voodoo?
		$this->getDomElement()->nodeValue = NULL;
	}

	/**
	 * Get the value from an option xml element, either the value attribute or
	 * the text content
	 */
	private function _getOptionValue(DOMElement $option)
	{
		return $option->hasAttribute('value') ?
			$option->getAttribute('value') : $option->textContent;
	}

	/**
	 * Get the label of the optgroup, or false if the option doesn't have one
	 */
	private function _getOptgroupLabel(DOMElement $option)
	{
		if($option->parentNode->tagName == 'optgroup')
		{
			return $option->parentNode->getAttribute('label');
		}

		return false;
	}

	/**
	 * Get an array of options in the select, these are represented as an array
	 * of arrays which contain three items, the tag content, the attributes and
	 * the optgroup (false for none)
	 * @return array
	 */
	public function getOptions()
	{
		$options = array();
		foreach($this->_options as $option)
		{
			$options[] = array(
				$option->textContent,
				$this->_getDomElementAttributes($option),
				$this->_getOptgroupLabel($option)
				);
		}

		return $options;
	}
}

?>
