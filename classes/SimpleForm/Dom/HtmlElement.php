<?php

/**
 * A DOM-wrapper implementation of a {@link SimpleForm_HtmlElement}
 */
class SimpleForm_Dom_HtmlElement implements SimpleForm_HtmlElement
{
	private $_element;

	/**
	 * Constructor
	 */
	function __construct(DOMElement $element)
	{
		$this->_element = $element;
	}

	/**
	 * Gets the internal {@link DOMElement}
	 */
	public function getDomElement()
	{
		return $this->_element;
	}

	/**
	 * Gets the internal {@link getDomDocument}
	 */
	public function getDomDocument()
	{
		return $this->_element->ownerDocument;
	}

	/**
	 * Gets a hash of the object, which is unique within the document
	 */
	public function getHash()
	{
		return spl_object_hash($this->_element);
	}

	/* (non-phpdoc)
	 * @see SimpleForm_HtmlElement::getInnerText()
	 */
	public function getInnerText()
	{
		$text = NULL;

		foreach($this->getDomElement()->childNodes as $node)
		{
			if($node->nodeType == XML_TEXT_NODE && !empty($node->nodeValue))
			{
				$text .=	$node->nodeValue;
			}
		}

		return $text;
	}

	/* (non-phpdoc)
	 * @see SimpleForm_HtmlElement::setInnerText()
	 */
	public function setInnerText($text)
	{
		$element = $this->_element;
		$document = $element->ownerDocument;
		$newNode = $document->createTextNode($text);
		$referenceNode = false;

		// find the first text node, delete all others
		foreach($element->childNodes as $node)
		{
			if($node->nodeType == XML_TEXT_NODE)
			{
				if(!$referenceNode)
				{
					$referenceNode = $node;
				}
				else
				{
					$element->removeChild($node);
				}
			}
		}

		// if a reference node was found, prepend before it
		if($referenceNode)
		{
			$element->insertBefore($newNode, $referenceNode);
			$element->removeChild($referenceNode);
		}
		else
		{
			$element->appendChild($newNode);
		}
	}

	/* (non-phpdoc)
	 * @see SimpleForm_HtmlElement::
	 */
	public function serialize()
	{
		$element = $this->_element;
		$document = $element->ownerDocument;
		return $document->saveXML($element);
	}

	/* (non-phpdoc)
	 * @see SimpleForm_HtmlElement::
	 */
	public function getTagName()
	{
		$element = $this->_element;
		return $element->tagName;
	}

	/* (non-phpdoc)
	 * @see SimpleForm_HtmlElement::getAttributes()
	 */
	public function getAttributes()
	{
		$element = $this->_element;
		$attributes = array();

		if($element->hasAttributes())
		{
			foreach($element->attributes as $attr)
			{
				$attributes[$attr->name] = $attr->value;
			}
		}

		return $attributes;
	}

	/* (non-phpdoc)
	 * @see SimpleForm_HtmlElement::getAttribute()
	 */
	public function getAttribute($name)
	{
		return $this->_element->getAttribute($name);
	}

	/* (non-phpdoc)
	 * @see SimpleForm_HtmlElement::hasAttribute()
	 */
	public function hasAttribute($name)
	{
		return $this->_element->hasAttribute($name);
	}

	/* (non-phpdoc)
	 * @see SimpleForm_HtmlElement::setAttribute()
	 */
	public function setAttribute($name,$value)
	{
		$this->_element->setAttribute($name,$value);
	}

	/* (non-phpdoc)
	 * @see SimpleForm_HtmlElement::removeAttribute()
	 */
	public function removeAttribute($name)
	{
		$this->_element->removeAttribute($name);
	}

	/* (non-phpdoc)
	 * @see SimpleForm_HtmlElement::clearAttributes()
	 */
	public function clearAttributes()
	{
		foreach($this->getAttributes() as $key=>$value)
		{
			$this->_element->removeAttribute($key);
		}
	}

	/* (non-phpdoc)
	 * @see SimpleForm_HtmlElement::getClasses()
	 */
	public function getClasses()
	{
		return preg_split('/\s+/',$this->getAttribute('class'),
			-1, PREG_SPLIT_NO_EMPTY);
	}

	/* (non-phpdoc)
	 * @see SimpleForm_HtmlElement::hasClass()
	 */
	public function hasClass($class)
	{
		return in_array($class,$this->getClasses());
	}

	/* (non-phpdoc)
	 * @see SimpleForm_HtmlElement::addClass()
	 */
	public function addClass($class)
	{
		if(!$this->hasClass($class))
		{
			$this->setAttribute('class',$this->getAttribute('class')." $class");
		}
	}

	/* (non-phpdoc)
	 * @see SimpleForm_HtmlElement::removeClass()
	 */
	public function removeClass($class)
	{
		$this->setAttribute('class',
			preg_replace('/(^|\b)'.preg_quote($class,'/').'(\b|$)/','',
				$this->getAttribute('class')));
	}

	/**
	 * Gets attributes of an {@link DOMElement} as an array
	 * @return array
	 */
	protected function _getDOMElementAttributes(DOMElement $el)
	{
		$attrs = array();

		foreach($el->attributes as $attr)
		{
			$attrs[$attr->name] = $attr->value;
		}

		return $attrs;
	}

	/**
	 * Sets attributes of an {@link DOMElement} from an array
	 * @param $attrs array
	 */
	protected function _setDOMElementAttributes(DOMElement $el, array $attrs)
	{
		foreach($attrs as $key=>$value)
		{
			$el->setAttribute($key,$value);
		}
	}
}

?>
