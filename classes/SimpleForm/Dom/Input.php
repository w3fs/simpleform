<?php

/**
 * Represents an Input element, which is used for file uploads, checkboxes,
 * text inputs, radio buttons, submit buttons, normal buttons, image buttons,
 * etc. Due to this, there is some complexities represented in the
 * {@link getValue()} and {@link setValue()} methods.
 *
 * @see http://www.w3.org/TR/html4/interact/forms.html#h-17.4
 * @author Lachlan Donald <lachlan@sitepoint.com>
 */
class SimpleForm_Dom_Input
	extends SimpleForm_Dom_AbstractFormElement
	implements SimpleForm_Input
{
	/**
	 * Constructor
	 */
	function __construct(DOMElement $element)
	{
		parent::__construct($element);
	}

	/* (non-phpdoc)
	 * @see SimpleForm_FormElement::getValue
	 */
	function getValue()
	{
		return $this->hasAttribute('value') ? $this->getAttribute('value') : NULL;
	}

	/* (non-phpdoc)
	 * @see SimpleForm_FormElement::setValue
	 */
	function setValue($value)
	{
		if($this->getType() == 'checkbox' || $this->getType() == 'radio')
		{
			if (!is_array($value))
			{
				$value = array($value);
			}

			if(in_array($this->getValue(), $value))
			{
				$this->setAttribute('checked','checked');
			}
			else
			{
				$this->removeAttribute('checked');
			}
		}
		else
		{
			$this->setAttribute('value',$value);
		}
	}

	/* (non-phpdoc)
	 * @see SimpleForm_FormElement::isReadOnly
	 */
	function isReadOnly()
	{
		return in_array($this->getType(), array('submit','button','reset','file','hidden','image'));
	}

	/* (non-phpdoc)
	 * @see SimpleForm_FormElement::clear
	 */
	function clear()
	{
		if($this->getType() != 'checkbox' && $this->getType() != 'radio')
		{
			$this->removeAttribute('value');
		}

		$this->removeAttribute('checked');
	}

	/* (non-phpdoc)
	 * @see SimpleForm_Input::setType()
	 */
	function setType($type)
	{
		return $this->setAttribute('type', $type);
	}

	/* (non-phpdoc)
	 * @see SimpleForm_Input::getType()
	 */
	function getType()
	{
		// input elements default to text
		return $this->hasAttribute('type') ? $this->getAttribute('type') : 'text';
	}

	/* (non-phpdoc)
	 * @see SimpleForm_Input::isCheckable()
	 */
	function isCheckable()
	{
		return
			($this->getType() == 'checkbox' || $this->getType() == 'radio');
	}

	/* (non-phpdoc)
	 * @see SimpleForm_Input::isChecked()
	 */
	function isChecked()
	{
		return
			($this->isCheckable() && $this->getAttribute('checked') == 'checked');
	}
}

?>