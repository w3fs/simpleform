<?php

/**
 * An form object-model which corresponds to an HTML form and its form elements.
 * Provides an API for querying and manipulating the form.
 * @author Lachlan Donald <lachlan@ljd.cc>
 * @version 3.0
 */
class SimpleForm_Form
{
	// private members
	private $_model;
	private $_request;
	private $_autoload=true;

	/**
	 * @var string the form field name used as a submit marker
	 */
	const SUBMIT_MARKER = 'simpleform_submit_marker';

	/**
	 * @var string the form field recognized as cancel
	 */
	const CANCEL_MARKER = 'cancel';

	/**
	 * Constructor, the model parameter defaults to the internal DOM
	 * implementation which requires PHP5.2+
	 * @param $model object a custom {@link SimpleForm_FormModel}
	 */
	function __construct(SimpleForm_FormModel $model=null)
	{
		// default to a DOM model implementation
		if(is_null($model)) $model = new SimpleForm_Dom_FormModel();

		$this->_model = $model;
		$this->_request = $this->_createHttpRequest();
	}

	/**
	 * Gets a form element with the provided identifier
	 * @param $id string the identifier of the element to retrieve
	 * @return object an instance of {@link SimpleForm_FormElement}
	 * @throws SimpleForm_Exception if no matching element is found
	 * @see SimpleForm_FormElementMap::getElementById()
	 */
	function getElementById($id)
	{
		return $this->_model->getElementById($id);
	}

	/**
	 * Gets all elements in the form with corresponding name attribute.
	 * @param $name mixed either a single name or an array of names
	 * @return array an array of {@link SimpleForm_FormElement} objects
	 * @see SimpleForm_FormElementMap::getElementByName()
	 */
	function getElementsByName($name)
	{
		return $this->_model->getElementsByName($name);
	}

	/**
	 * Gets all elements in the form with corresponding tag name.
	 * @param $name mixed either a single name or an array of names
	 * @return array an array of {@link SimpleForm_FormElement} objects
	 * @see SimpleForm_FormElementMap::getElementByName()
	 */
	function getElementsByTagName($tagname)
	{
		return $this->_model->getElementsByTagName($tagname);
	}

	/**
	 * Gets all elements in the form
	 * @return array an array of {@link SimpleForm_FormElement} objects
	 */
	public function getAllElements()
	{
		return $this->_model->getAllElements();
	}

	/**
	 * Returns all the labels in the form
	 * @return array an array of labels
	 */
	function getAllLabels()
	{
		return $this->_model->getAllLabels();
	}

	/**
	 * Returns labels for a particular element identifier
	 * @throws SimpleForm_Exception if no matching element is found
	 * @return array an array of labels
	 */
	function getLabelsByElementId($id)
	{
		return $this->_model->getLabelsByElementId($id);
	}

	/**
	 * Injects a hidden <INPUT /> element into a form, at the end
	 */
	public function injectHiddenElement($name, $value)
	{
		$this->_model->injectHiddenElement($name, $value);
	}

	/**
	 * Creates the internally used http request
	 */
	protected function _createHttpRequest()
	{
		return new SimpleForm_HttpRequest();
	}

	/**
	 * Overrides the default {@link SimpleForm_HttpRequest} instance
	 * used internally for interacting with the http request
	 */
	public function setHttpRequest(SimpleForm_HttpRequest $request)
	{
		$this->_request = $request;
	}

	/**
	 * Determines whether {@link loadRequest()} is automatically called
	 * when a form is parsed.
	 * @param $value bool
	 */
	public function setAutoload($value)
	{
		$this->_autoload = $value;
	}

	/**
	 * Populates the form model, replacing any previous state created with
	 * a previous parse operation
	 * @param $html mixed html for a form, or a php stream
	 */
	public function parse($html)
	{
		$this->_model->parse($html);
		if($this->_autoload && $this->isSubmitted()) $this->loadRequest();
	}

	/**
	 * Gets the form's id attribute
	 */
	public function getId()
	{
		return $this->_model->getRootElement()->getAttribute('id');
	}

	/**
	 * Gets the form's action attribute
	 */
	public function getAction()
	{
		return $this->_model->getRootElement()->getAttribute('action');
	}

	/**
	 * Sets the form's action attribute
	 * @return object the form for method chaining
	 */
	public function setAction($action)
	{
		$this->_model->getRootElement()->setAttribute('action', $action);
		return $this;
	}

	/**
	 * Gets the form's method attribute, either get or post
	 */
	public function getMethod()
	{
		return $this->_model->getRootElement()->getAttribute('method');
	}

	/**
	 * Sets the form's method attribute, either get or post
	 */
	public function setMethod($method)
	{
		$this->_model->getRootElement()->setAttribute('method', $method);
		return $this;
	}

	/**
	 * Detects if the form has been submitted
	 * @return boolean true if the form has been submitted
	 */
	public function isSubmitted()
	{
		return strtolower($this->_request->getMethod()) == $this->getMethod() &&
			$this->_request->getParam(self::SUBMIT_MARKER) == $this->getId();
	}

	/**
	 * Detects if the form has been cancelled. This is determined by looking for
	 * a request parameter named "cancel".
	 * @return boolean true if the form has been cancelled
	 */
	public function isCancelled()
	{
		return strtolower($this->_request->getMethod()) == $this->getMethod() &&
		$this->_request->getParam(self::CANCEL_MARKER) != false;
	}

	/**
	 * Partially expand a data array, top-level numeric indexed arrays as
	 * arrays
	 */
	private function _partialExpand($array)
	{
		$partialArray = array();

		foreach($array as $key=>$value)
		{
			if(preg_match('/^(.+?)\[\d+\]$/',$key,$m))
			{
				$partialArray[$m[1].'[]'][] = $value;
			}
		}

		return $partialArray;
	}

	/**
	 * Loads an associative array of data into the form
	 * @param $data array an array of key value pairs
	 * @param $force boolean if true, even read only elements have their value set
	 */
	function load($data, $force=false)
	{
		$data = SimpleForm_FormArrayHelper::collapse($data);
		$expanded = $this->_partialExpand($data);
		$counters = array();

		// index elements by name
		foreach ($this->getAllElements() as $element)
		{
			// skip elements that are read-only
			if(!$force && $element->isReadOnly()) continue;

			$name = $element->getName();
			$radioOrCheck = $element instanceof SimpleForm_Input &&
				in_array($element->getType(),array('radio','checkbox'));
			$value = false;

			// check for an exact match
			if(array_key_exists($name,$data))
			{
				$value = $data[$name];
			}
			else if(preg_match('/\[\]$/',$name,$m))
			{
				// radio and check inputs get all values from expanded data
				if($radioOrCheck)
				{
					if(array_key_exists($name,$expanded))
					{
						$value = $expanded[$name];
					}
				}
				else
				{
					// initialize the counter
					if(!isset($counters[$name])) $counters[$name] = 0;

					// add the counter to the [] name
					$i = $counters[$name];
					$ikey = preg_replace('/\[\]$/',"[$i]",$name);

					if(array_key_exists($ikey,$data))
					{
						$counters[$name]++;
						$value = $data[$ikey];
					}
				}
			}

			// apply the found value
			if($value !== false)
			{
				$element->clear();
				$element->setValue($value);
				$value = false;
			}
		}
	}

	/**
	 * Gets the parameters from the request that correspond to the forms
	 * method
	 * @return array the request params, either get or post
	 */
	function getRequestParams()
	{
		return ($this->getMethod() == 'post') ?
		$this->_request->getParams(SimpleForm_HttpRequest::METHOD_POST) :
			$this->_request->getParams(SimpleForm_HttpRequest::METHOD_GET);
	}

	/**
	 * Loads an associative array of data into the form from the relevant request variable.
	 * This method also adds in NULL values for empty checkbox and radio button fields.
	 */
	function loadRequest()
	{
		$data = SimpleForm_FormArrayHelper::collapse($this->getRequestParams());

		// add NULL values for missing elements
		foreach(array_keys($this->getValues()) as $name)
		{
			if(!isset($data[$name])) $data[$name] = NULL;
		}

		return $this->load($data);
	}

	/**
	 * Returns an array of values as they would be submitted from a browser
	 * @return array the object as key-value pairs
	 */
	function getValues()
	{
		$array = array();

		// create an array of key=>value pairs in an array wrapper
		foreach($this->getAllElements() as $element)
		{
			// skip elements with no name
			if(!$name = $element->getName())
			{
				continue;
			}

			$isArray = preg_match('/\[\]$/', $name);

			// strip the trailing []
			if($isArray) $name = substr($name,0,-2);

			// checkbox/radio button
			if($element instanceof SimpleForm_Input && $element->isCheckable())
			{
				if($element->getAttribute('checked') == 'checked')
				{
					if($isArray)
					{
						$array[$name] []= $element->getValue();
					}
					else
					{
						$array[$name] = $element->getValue();
					}
				}
				else if(!isset($array[$name]))
				{
					$array[$name] = array();
				}
			}
			// other input type
			else
			{
				if($isArray)
				{
					$array[$name] []= $element->getValue();
				}
				else
				{
					$array[$name] = $element->getValue();
				}
			}
		}

		$notnull = create_function('$x','return !is_null($x);');

		// strip nulls out of sub-arrays
		foreach($array as $key=>$value)
		{
			if(is_array($value)) $array[$key] = array_filter($value,$notnull);
		}

		return $array;
	}

	/**
	 * Return the form as an array, like {@link getValues()}, except for fully
	 * expanded, so keys such as test[a][b] => 'blah' become expanded, nested
	 * arrays, much like PHP would return from a POST of the form.
	 */
	function getExpandedValues()
	{
		return SimpleForm_FormArrayHelper::expand($this->getValues());
	}

	/**
	 * Returns the html of the form
	 * @param $echo boolean if true, the html is echoed, otherwise its returned
	 */
	public function output($echo=true)
	{
		$html = $this->_model->serialize();

		// build a submit marker
		$submitMarker = sprintf("\n".'<input type="hidden" name="%s" value="%s"/>',
			SimpleForm_Form::SUBMIT_MARKER, $this->getId());

		// replace the form close tag with the submit marker
		$html = preg_replace("#(\s*)</form>#ims",$submitMarker.'\1</form>',$html);

		if($echo) echo $html;
		else return $html;
	}
}

?>
