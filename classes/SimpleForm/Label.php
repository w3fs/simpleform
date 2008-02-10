<?php

/**
 * A label for a form element
 * @see http://www.w3.org/TR/html4/interact/forms.html#h-17.9
 */
interface SimpleForm_Label extends SimpleForm_HtmlElement
{
	/**
	 * Gets form element associated with the label
	 * @return object an instance of a {@link SimpleForm_FormElement}
	 */
	function getAssociatedElement();

	/**
	 * Gets the label text
	 */
	function getText();

	/**
	 * Sets the label text, see {@link SimpleForm_HtmlElement::setInnerText} for
	 * how this function behaves when the label has a mixture of text and
	 * element children.
	 */
	function setText($text);

	/**
	 * Returns a text-only summary of the label contents, generally the first
	 * text-node found in the subtree, with trailing non-word characters stripped
	 */
	function getLabelSummary();
}

?>
