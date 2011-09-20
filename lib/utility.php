<?php

/**
 * Contains utility classes
 *
 * @package	utility
 * @author	loganfranken
 */

/**
 * Utility class for retrieving GET and POST variables
 *
 * @package	utility
 * @author	loganfranken
 */
class Request
{
	/**
	 * Retrieves a GET variable
	 *
	 * @param	string	$varName	Name of the GET variable
	 *
	 * @return	mixed	Value of the GET variable, or false if the GET variable is undefined
	 *
	 * @access	public
	 * @static
	 */
	public static function getUrlVar($varName)
	{
		return isset($_GET[$varName]) ? $_GET[$varName] : false;
	}
	
	/**
	 * Retrieves a POST variable
	 *
	 * @param	string	$varName	Name of the POST variable
	 *
	 * @return	mixed	Value of the POST variable, or false if the POST variable is undefined
	 *
	 * @access	public
	 * @static
	 */
	public static function getPostVar($varName)
	{
		return isset($_POST[$varName]) ? $_POST[$varName] : false;
	}
}

/**
 * Represents a response from a request to a web handler
 *
 * @package	utility
 * @author	loganfranken
 */
abstract class Response
{
	/**
	 * Message describing an error associated with a response
	 * @var	string
	 */
	public $error;
	
	/**
	 * Requested data
	 * @var	mixed
	 */
	public $data;
	
	/**
	 * Returns the response serialized as JSON
	 *
	 * @return	string	Response serialized as JSON
	 *
	 * @access	public
	 */
	public function __toString()
	{
		return json_encode($this);
	}
}

/**
 * Represents an error response from a request to a web handler
 *
 * @package	utility
 * @author	loganfranken
 */
class ErrorResponse extends Response
{
	public function __construct($error)
	{
		$this->error = $error;
	}
}

/**
 * Represents a successful response from a request to a web handler
 *
 * @package	utility
 * @author	loganfranken
 */
class SuccessResponse extends Response
{
	public function __construct($data)
	{
		$this->data = $data;
	}
}

?>