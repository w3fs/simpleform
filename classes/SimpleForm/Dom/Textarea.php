<?php

/**
 * A DOM implementation of a textarea field
 */
class SimpleForm_Dom_Textarea extends SimpleForm_Dom_AbstractFormElement
{
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
		$this->setValue(NULL);
	}

	/* (non-phpdoc)
	 * @see SimpleForm_FormElement::getValue
	 */
	public function getValue()
	{
		$element = $this->getDomElement();
		$text = '';

		// first remove everything
		foreach($element->childNodes as $child)
		{
			$text .= $this->getDomDocument()->saveXML($child);
		}

		return htmlspecialchars_decode($text);
	}

	/* (non-phpdoc)
	 * @see SimpleForm_FormElement::setValue
	 */
	public function setValue($value)
	{
		$element = $this->getDomElement();

		// removes all children
		$element->nodeValue = NULL;

		// replace with a text node
		$element->appendChild($this->getDomDocument()->createTextNode($value));
	}
}

?>
