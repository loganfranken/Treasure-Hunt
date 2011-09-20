<?php

/**
 * Contains logic for receiving requests for interacting with the Treasure Hunt system and
 * routing to the appropriate handler
 *
 * @package	request
 * @author	loganfranken
 */

require_once('core.php');
require_once('model.php');
require_once('utility.php');
require_once('validation.php');

// Set JSON as response type
header('Content-type: application/json');

// Handle all extraneous errors with generic response
set_error_handler('onError');
error_reporting(0);

function onError($errno, $errstr, $errfile, $errline)
{
	echo (new ErrorResponse('An unknown error has occurred'));
	return false;
}

try
{
	// Retrieve variables
	$action = Request::getPostVar('action');
	$itemName = Request::getPostVar('item-name');
	$latitude = Request::getPostVar('latitude');
	$longitude = Request::getPostVar('longitude');

	// Execute action
	switch($action)
	{
		case 'bury':
			echo RequestHandler::handleBuryRequest($itemName, $latitude, $longitude);
			break;
			
		case 'dig':
			echo RequestHandler::handleDigRequest($latitude, $longitude);
			break;
			
		case 'search':
			echo RequestHandler::handleSearchRequest($latitude, $longitude);
			break;
			
		default:
			echo (new ErrorResponse('Invalid action'));
			break;
	}
}
catch(Exception $ex)
{
	echo (new ErrorResponse('An unknown error has occurred'));
}

/**
 * Handles requests for interacting with the Treasure Hunt system
 *
 * @package	request
 * @author	loganfranken
 */
class RequestHandler
{
    /**
     * Handles requests to "bury" a treasure
     *
     * @param	string 		$itemName	Name of the treasure to bury
     * @param	float  		$latitude	Latitude of the location to bury the treasure
     * @param	float  		$longitude	Longitude of the location to bury the treasure
     *
     * @return	Response	Response describing the results of the bury request. If successful,
	 *						a SuccessResponse only containing true is returned. If an error occurs,
	 *						an ErrorResponse containing the error message is returned
     *
     * @access public
     * @static
     */
	public static function handleBuryRequest($itemName, $latitude, $longitude)
	{
		// Validate
		if(!Validator::validateItemName($itemName, $error)
			|| !Validator::validateLatitude($latitude, $error)
			|| !Validator::validateLongitude($longitude, $error))
		{
			return new ErrorResponse($error);
		}
		
		// Check if treasure is already buried
		if(Model::isTreasureBuried($latitude, $longitude))
		{
			return new ErrorResponse('A treasure is already buried here');
		}
		
		// Bury treasure
		Model::buryTreasure($itemName, $latitude, $longitude);
		
		return new SuccessResponse(true);
	}

	/**
     * Handles requests to "dig" for a treasure
     *
     * @param	float  		$latitude  Latitude of the location to dig for the treasure
     * @param	float  		$longitude Longitude of the location to dig for the treasure
     *
     * @return	Response	Response describing the results of the dig request. If successful,
	 *						a SuccessResponse containing the treasure is returned. If no treasure
	 *						is found, a SuccessResponse is still returned, but it contains NULL.
	 *						If an error occurs, an ErrorResponse containing the error message is
	 *						returned
     *
     * @access public
     * @static
     */
	public static function handleDigRequest($latitude, $longitude)
	{
		// Validate
		if(!Validator::validateLatitude($latitude, $error)
			|| !Validator::validateLongitude($longitude, $error))
		{
			return new ErrorResponse($error);
		}
		
		// Dig for treasure
		$treasure = Model::digForTreasure($latitude, $longitude);
		
		// If a treasure was found, remove sensitive information
		if($treasure !== NULL)
		{
			$treasure->id = NULL;
		}

		return new SuccessResponse($treasure);
	}
	
	/**
     * Handles requests to "search" for a treasure
     *
     * @param	float  		$latitude  Latitude of the location to bury the treasure
     * @param	float  		$longitude Longitude of the location to bury the treasure
     *
     * @return	Response	Response describing the results of the search request. If successful,
	 *						a SuccessResponse containing a nearby treasure is returned. If no
	 *						treasure is found, a SuccessResponse is still returned, but it contains
	 *						NULL. If an error occurs, an ErrorResponse containing the error message
	 *						is returned
     *
     * @access public
     * @static
     */
	public static function handleSearchRequest($latitude, $longitude)
	{
		// Validate
		if(!Validator::validateLatitude($latitude, $error)
			|| !Validator::validateLongitude($longitude, $error))
		{
			return new ErrorResponse($error);
		}

		// Execute query
		$treasure = Model::searchForTreasure($latitude, $longitude);

		// If a treasure was found, remove sensitive information
		if($treasure !== NULL)
		{
			$treasure->id = NULL;
			$treasure->name = NULL;
		}
			
		return new SuccessResponse($treasure);
	}
}

?>