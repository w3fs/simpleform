<?php

/**
 * A DOM implementation of a label
 */
class SimpleForm_Dom_Label
	extends SimpleForm_Dom_HtmlElement
	implements SimpleForm_Label
{
	private $_element;
	private $_for;

	/**
	 * Constructor
	 */
	function __construct(DOMElement $element, SimpleForm_FormElement $for)
	{
		parent::__construct($element);
		$this->_for = $for;
	}

	/* (non-phpdoc)
	 * @see SimpleForm_Label::getAssociatedElement()
	 */
	function getAssociatedElement()
	{
		return $this->_for;
	}

	/**
	 * Gets the associated element id, or false if none exists or has no id
	 * @return string
	 * @deprecated SimpleForm_Dom_Label::getAssociatedElement()
	 */
	function getAssociatedElementId()
	{
		$element = $this->getAssociatedElement();
		return (!is_null($element) && $id = $element->getId()) ? $id : false;
	}

	/* (non-phpdoc)
	 * @see SimpleForm_Label::getText()
	 */
	function getText()
	{
		return trim($this->getInnerText());
	}

	/* (non-phpdoc)
	 * @see SimpleForm_Label::setText()
	 */
	function setText($text)
	{
		return $this->setInnerText($text);
	}

	/* (non-phpdoc)
	 * @see SimpleForm_Label::getAssociatedElement()
	 */
	function getLabelSummary()
	{
		$item = 0;
		$children = $this->getDomElement()->childNodes;

		// move forward will we find a non-formelement node
		while($children->item($item)->nodeType == XML_ELEMENT_NODE &&
			in_array($children->item($item)->tagName,
				array('input','button','textarea','select')))
		{
			$item++;
		}

		// could there really be no label?
		if($item > $children->length)
		{
			return null;
		}

		$summary = $this->getDomDocument()->saveXML($children->item($item));
		return trim(preg_replace('/[^\w\s-]+/','',strip_tags($summary)));
	}
}

?>
