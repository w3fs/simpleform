<?php

/**
 * A model of the form elements that exist within a form, provides a variety
 * of ways of querying and accessing form elements, similar to DOM. At it's
 * core, the model is collection of {@link SimpleForm_FormElement} objects.
 */
interface SimpleForm_FormModel
{
	/**
	 * Parse either a string of html or the contents of a PHP input
	 */
	public function parse($html);

	/**
	 * Gets a form element with the provided identifier
	 * @throws SimpleForm_Exception if no matching element is found
	 */
	public function getElementById($id);

	/**
	 * Gets all elements in the form with corresponding name attribute.
	 * @param $name mixed either a single name or an array of names
	 * @return array an array of {@link SimpleForm_FormElement} objects
	 */
	public function getElementsByName($name);

	/**
	 * Gets all elements with a particular tagname
	 * @param $tagname mixed either a single tagname or an array of tagnames
	 * @return array an array of {@link SimpleForm_FormElement} objects
	 */
	public function getElementsByTagName($tagname);

	/**
	 * Gets all elements from the form
	 * @return array an array of {@link SimpleForm_FormElement} objects
	 */
	public function getAllElements();

	/**
	 * Returns all the labels in the form
	 * @return array an array of labels
	 */
	function getAllLabels();

	/**
	 * Returns labels for a particular element identifier
	 * @throws SimpleForm_Exception if no matching element is found
	 * @return array an array of labels
	 */
	function getLabelsByElementId($id);

	/**
	 * Injects a hidden <INPUT /> element into a form, at the end
	 */
	public function injectHiddenElement($name, $value);

	/**
	 * Inject arbitrary HTML, either at the start of the form, or before a
	 * specified element
	 */
	public function injectHtml($html, SimpleForm_FormElement $ctx=null);

	/**
	 * Gets the root form element
	 * @return SimpleForm_Input_HtmlElement
	 */
	public function getRootElement();

	/**
	 * Gets the serialized html form
	 */
	public function serialize();
}

?>
