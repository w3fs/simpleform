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
interface SimpleForm_Input extends SimpleForm_HtmlElement
{
	/**
	 * Set the type of the input
	 */
	function setType($type);
	/**
	 * Gets the type of the input
	 */
	function getType();

	/**
	 * Returns true if the element is a radio or checkbox
	 */
	function isCheckable();

	/**
	 * Returns true if the element is a radio or checkbox and is checked
	 */
	function isChecked();
}

?>
