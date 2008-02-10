<?php

/**
 * A PHP5 DOM parser
 */
class SimpleForm_Dom_Parser
{
	/**
	 * Parses either a string of html or the html contents of a stream and
	 * returns entire document, which will have had a basic HTML structure added
	 * to it
	 */
	public function parse($html)
	{
		$prev = libxml_use_internal_errors(true);

		$doc = new DOMDocument();
		$doc->loadHTML($this->_readString($html));

		$errors = libxml_get_errors();

		// clear errors, reset error checking
		libxml_clear_errors();
		libxml_use_internal_errors($prev);

		if (!$doc)
		{
			throw new SimpleForm_ParseException($errors[0]);
		}

		return $doc;
	}

	/**
	 * Parses either a string of html or the contents of a stream and
	 * returns the top level element
	 */
	public function parseElement($html)
	{
		$doc = $this->parse($html);
		$body = $doc->getElementsByTagName('body')->item(0);

		// if the body element has no children, the document was empty
		if(!$body->hasChildNodes())
		{
			throw new SimpleForm_Exception('No elements found in document');
		}

		return $body->childNodes->item(0);
	}

	/**
	 * Read the contents of either a string or a stream
	 */
	private function _readString($mixed)
	{
		if(is_string($mixed))
		{
			return $mixed;
		}
		else
		{
			throw new BadMethodCallException('Only string parsing is implemented');
		}
	}
}

?>
