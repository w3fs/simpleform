<?php

/**
 * An abstract implementation of a form element that provides some basic
 * functionality such as getName, getId, etc
 */
abstract class SimpleForm_Dom_AbstractFormElement
	extends SimpleForm_Dom_HtmlElement
	implements SimpleForm_FormElement
{
	private $_labels;

	/**
	 * Constructor
	 */
	function __construct(DOMElement $element, array $labels=array())
	{
		parent::__construct($element);
		$this->_labels = array();
	}

	/* (non-phpdoc)
	 * @see SimpleForm_FormElement::getName()
	 */
	public function getName()
	{
		return $this->getAttribute('name');
	}

	/* (non-phpdoc)
	 * @see SimpleForm_FormElement::getId()
	 */
	public function getId()
	{
		return $this->getAttribute('id');
	}

	/**
	 * Adds an associated label to the form element
	 */
	public function addAssociatedLabel(SimpleForm_Label $label)
	{
		$this->_labels[] = $label;
	}

	/**
	 * Gets all associated labels for the form element
	 * @return array an array of {@link SimpleForm_Label} instances
	 */
	public function getAssociatedLabels()
	{
		return $this->_labels;
	}
}

?>
