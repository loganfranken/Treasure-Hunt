<?php

/**
 * Contains validation classes
 *
 * @package	validation
 * @author	loganfranken
 */

 /**
 * General validation class
 *
 * @package	validation
 * @author	loganfranken
 */
class Validator
{
	/**
	 * Validates the name for a buried treasure item
	 *
	 * @param	string  $itemName	Name of the item
	 * @param	string	$error		Message describing validation error, if validation fails
	 *
	 * @return	bool	True if validation is successful, false otherwise
	 *
	 * @access	public
	 * @static
	 */
	public static function validateItemName($itemName, &$error)
	{
		if(!$itemName)
		{
			$error = 'No item name provided';
			return false;
		}

		if(!preg_match('/^[a-z0-9][a-z0-9 ]*$/i', $itemName))
		{
			$error = 'Item name must be at least one character long and can only contain letters, '
						. 'numbers, and spaces';
			return false;
		}
		
		if(strlen($itemName) > 100)
		{
			$error = 'Item name must be less than 100 characters long';
			return false;
		}
		
		return true;
	}

	/**
	 * Validates a latitude value
	 *
	 * @param	float	$latitude	Latitude value
	 * @param	string	$error		Message describing validation error, if validation fails
	 *
	 * @return	bool	True if validation is successful, false otherwise
	 *
	 * @access	public
	 * @static
	 */
	public static function validateLatitude($latitude, &$error)
	{
		return self::validateGeoCoord('latitude', $latitude, $error);
	}

	/**
	 * Validates a longitude value
	 *
	 * @param	float	$latitude	Longitude value
	 * @param	string	$error		Message describing validation error, if validation fails
	 *
	 * @return	bool	True if validation is successful, false otherwise
	 *
	 * @access	public
	 * @static
	 */
	public static function validateLongitude($longitude, &$error)
	{
		return self::validateGeoCoord('longitude', $longitude, $error);
	}

	/**
	 * Validates a latitude or longitude value
	 *
	 * @param	string	$geoCoordName	Name of the geographic coordinate (generally 'latitude' or
	 *									'longitude')
	 * @param	string	$geoCoord		Value of the geographic coordinate
	 * @param	string	$error			Message describing validation error, if validation fails
	 *
	 * @return	bool	True if validation is successful, false otherwise
	 *
	 * @access	public
	 * @static
	 */
	public static function validateGeoCoord($geoCoordName, $geoCoord, &$error)
	{
		if(!$geoCoord)
		{
			$error = 'No ' . $geoCoordName . ' provided';
			return false;
		}
		
		if(!is_numeric($geoCoord))
		{
			$error = 'Value for ' . $geoCoordName . ' must be numeric';
			return false;
		}
		
		return true;
	}
}

?>