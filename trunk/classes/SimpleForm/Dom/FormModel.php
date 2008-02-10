<?php

/**
* A {@link SimpleForm_FormModel} that translates to an underlying
* DOM implementation. Form elements are parsed and build lazily.
 */
class SimpleForm_Dom_FormModel implements SimpleForm_FormModel
{
	const MINIMAL_FORM='<form id="simpleform"></form>';

	private $_form;
	private $_doc;
	private $_elements;
	private $_labels;
	private $_xpath;

	/**
	 * Construct a minimal form model
	 */
	public function __construct($html=self::MINIMAL_FORM)
	{
		$this->parse($html);
	}

	/* (non-phpdoc)
	 * @see SimpleForm_FormModel::parse()
	 */
	public function parse($html)
	{
		$parser = new SimpleForm_Dom_Parser();
		$element = $parser->parseElement($html);

		// only support top level form tags for the minute
		if($element->tagName != 'form')
		{
			throw new SimpleForm_Exception("Top level tag must be a form");
		}

		// only support forms with identifiers
		if(!$element->hasAttribute('id'))
		{
			throw new SimpleForm_Exception("Form must have an identifier");
		}

		$this->_form = $element;
		$this->_doc = $element->ownerDocument;
		$this->_xpath = new DomXPath($this->_doc);
		$this->_elements = array();
		$this->_labels = array();

		// go through form elements, build them
		foreach(array('input','select','textarea') as $tag)
		{
			foreach($this->_doc->getElementsByTagName($tag) as $el)
			{
				// remove the submit marker to prevent outputting it again
				if($el->getAttribute('name') == SimpleForm_Form::SUBMIT_MARKER)
				{
					$el->parentNode->removeChild($el);
				}
				else
				{
					$formEl = $this->_createFormElement($el);
					$hash = $formEl->getHash();
					$this->_elements[$hash] = $formEl;
				}
			}
		}

		// go through each label, build them
		foreach($this->_doc->getElementsByTagName('label') as $el)
		{
			$formEl = $this->_createLabelElement($el);
			$hash = $formEl->getHash();
			$this->_labels[$hash] = $formEl;
		}
	}

	/* (non-phpdoc)
	 * @see SimpleForm_FormModel::getElementById()
	 */
	public function getElementById($id)
	{
		if(empty($id))
		{
			throw new InvalidArgumentException("Identifiers can't be blank");
		}

		$elements = $this->_getElementsByAttribute('id', $id);
		if(count($elements) == 0)
		{
			throw new SimpleForm_Exception(
				"Failed to find an element with the requested id of '$id'");
		}
		else
		{
			return $elements[0];
		}
	}

	/* (non-phpdoc)
	 * @see SimpleForm_FormModel::getElementsByName()
	 */
	public function getElementsByName($name)
	{
		$results = array();
		if(!is_array($name)) $name=array($name);

		foreach($this->_elements as $element)
		{
			if(in_array($element->getName(), $name))
			{
				$hash = $element->getHash();
				$results[] = $element;
			}
		}

		return $results;
	}

	/**
	 * Searches parsed elements by attribute
	 */
	private function _getElementsByAttribute($attr, $value)
	{
		$results = $this->_xpath->query(sprintf("//*[@%s = '%s']",$attr,$value));
		$elements = array();

		foreach($results as $result)
		{
			$elements[] = $this->_createFormElement($result);
		}

		return $elements;
	}

	/* (non-phpdoc)
	 * @see SimpleForm_FormModel::getElementsByTagName()
	 */
	public function getElementsByTagName($tagname)
	{
		$results = array();
		if(!is_array($tagname)) $tagname=array($tagname);

		foreach($this->_elements as $element)
		{
			if(in_array($element->getTagName(), $tagname))
			{
				$results[] = $element;
			}
		}

		return $results;
	}

	/* (non-phpdoc)
	 * @see SimpleForm_FormModel::getAllElements()
	 */
	public function getAllElements()
	{
		return array_values($this->_elements);
	}

	/* (non-phpdoc)
	 * @see SimpleForm_FormModel::getAllLabels()
	 */
	function getAllLabels()
	{
		$labels = array();

		foreach($this->getAllElements() as $element)
		{
			foreach($element->getAssociatedLabels() as $label)
			{
				$labels[] = $label;
			}
		}

		return $labels;
	}

	/* (non-phpdoc)
	 * @see SimpleForm_FormModel::getLabelsByElementId()
	 */
	function getLabelsByElementId($id)
	{
		return $this->getElementById($id)->getAssociatedLabels();
	}

	/**
	 * Serialize a single element
	 */
	private function _serializeElement($element)
	{
		return $this->_doc->saveXML($element);
	}

	/* (non-phpdoc)
	 * @see SimpleForm_FormModel::serialize()
	 */
	public function serialize()
	{
		return $this->_serializeElement($this->_form);
	}

	/* (non-phpdoc)
	 * @see SimpleForm_FormModel::getAllElements()
	 */
	public function injectHiddenElement($name, $value)
	{
		// create the element
		$element = $this->_doc->createElement('input');
		$element->setAttribute('type','hidden');
		$element->setAttribute('name',$name);
		$element->setAttribute('value',$value);

		// append to the form
		$this->_form->appendChild($element);

		// add the form element
		$formElement = new SimpleForm_Dom_Input($element);
		$this->_elements[$formElement->getHash()] = $formElement;
	}

	/* (non-phpdoc)
	 * @see SimpleForm_FormModel::getAllElements()
	 */
	public function injectHtml($html, SimpleForm_FormElement $ctx=null)
	{
		throw new BadMethodCallException('Not implemented');
	}

	/* (non-phpdoc)
	 * @see SimpleForm_FormModel::getAllElements()
	 */
	public function getRootElement()
	{
		return new SimpleForm_Dom_HtmlElement($this->_form);
	}

	/**
	 * Create a form element and returns it, ensuring if the element
	 * was previously added that the previous instance is returned
	 * @return object the object that was created
	 */
	private function _createFormElement(DOMElement $element)
	{
		switch(strtolower($element->tagName))
		{
			case 'input':
				$formElement = new SimpleForm_Dom_Input($element);
				break;
			case 'select':
				$formElement = new SimpleForm_Dom_Select($element);
				break;
			case 'textarea':
				$formElement = new SimpleForm_Dom_Textarea($element);
				break;
		}

		if(!isset($element))
		{
			throw new SimpleForm_Exception('Unknown form element type '.
				$element->tagName);
		}
		else
		{
			// make an identity lookup
			$hash = $formElement->getHash();
			return isset($this->_elements[$hash]) ? $this->_elements[$hash] :
				$formElement;
		}
	}

	/**
	 * Create a label and returns it
	 * @return object the object that was created
	 */
	private function _createLabelElement(DOMElement $element)
	{
		$for = null;

		// find associated form element
		if($element->hasAttribute('for'))
		{
			try
			{
				$for = $this->getElementById($element->getAttribute('for'));
			}
			catch(SimpleForm_Exception $e)
			{
				trigger_error("Label references element id ".
					$element->getAttribute('for')." that doesn't exist");
			}
		}
		// otherwise look for an implicit association
		else if($els = $this->getElementsByTagName(array('input','select','textarea')))
		{
			if(count($els)>1)
			{
				throw new SimpleForm_Exception(
					"Multiple implicit label elements are illegal"
					);
			}
			else if(count($els)==1)
			{
				$for = $els[0];
			}
		}

		// TODO: should $for=NULL trigger an exception?
		$label = new SimpleForm_Dom_Label($element, $for);

		// make an identity lookup
		$hash = $label->getHash();
		$label = isset($this->_labels[$hash]) ? $this->_labels[$hash] : $label;

		// add the label to the associated element
		if(!is_null($for)) $for->addAssociatedLabel($label);

		return $label;
	}
}

?>
