<?php

/**
 * An element that is part of a form. All form elements have a concept
 * of value, which corresponds to the value that they will submit in the context
 * of a form submission.
 */
interface SimpleForm_FormElement extends SimpleForm_HtmlElement
{
	/**
	 * Gets the form elements id attribute
	 */
	public function getId();

	/**
	 * Gets the form elements name
	 */
	public function getName();

	/**
	 * Gets the value that the form element would submit on form submission
	 */
	public function getValue();

	/**
	 * Sets the form elements submitted value, often causes change in other
	 * attributes of the element, depending on the form element.
	 */
	public function setValue($value);

	/**
	 * Gets an array of {@link SimpleForm_Label} that are associated with the
	 * form element, either explicitly via id or implicitly
	 */
	public function getAssociatedLabels();

	/**
	 * Whether the form element will have values loaded into it, some elements
	 * don't, for instance a submit element
	 */
	public function isReadOnly();

	/**
	 * Clears the value from the element
	 */
	public function clear();
}

?>
