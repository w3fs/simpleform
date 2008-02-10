<?php

/**
 * An HTML element
 */
interface SimpleForm_HtmlElement
{
	/**
	 * Gets the inner text of the element, flattening all child text nodes
	 * into a single string. Whitespace text-nodes are dropped.
	 * @return string
	 */
	public function getInnerText();

	/**
	 * Overwrites the child text nodes, by deleting them and adding a new text
	 * node in the same position as the first occuring text node. If there are
	 * no text nodes, the provided text is appended to the end.
	 * @param $text string the text to set
	 * @return string
	 */
	public function setInnerText($text);

	/**
	 * Serializes all child nodes into text
	 * @return string
	 */
	public function serialize();

	/**
	 * Gets the tag name
	 * @return string
	 */
	public function getTagName();

	/**
	 * Gets an array of all the elements attributes
	 * @return array
	 */
	public function getAttributes();

	/**
	 * Gets a particular attribute, or NULL if it doesn't exist
	 * @param $name string the name of the attribute
	 * @return mixed string or NULL
	 */
	public function getAttribute($name);

	/**
	 * Determines if the element has a particular attribute
	 * @param $name string the name of the attribute
	 * @return bool
	 */
	public function hasAttribute($name);

	/**
	 * Sets a particular attribute
	 * @param $name string the name of the attribute
	 * @param $value string the value of the attribute
	 */
	public function setAttribute($name,$value);

	/**
	 * Removes an attribute
	 * @param $name string the name of the attribute
	 */
	public function removeAttribute($name);

	/**
	 * Deletes all attributes, such that subsequent checks on the attributes
	 * existance will fail
	 */
	public function clearAttributes();

	/**
	 * Returns the classes attribute as an array of class tokens
	 * @return array
	 */
	public function getClasses();

	/**
	 * Determines whether the class attribute has the class specified
	 * @param $class string
	 * @return bool
	 */
	public function hasClass($class);

	/**
	 * Adds a class to the class attribute, unless it already exists
	 * @param $class string
	 */
	public function addClass($class);

	/**
	 * Removes a class from the class attribute
	 * @param $class string
	 */
	public function removeClass($class);
}

?>
