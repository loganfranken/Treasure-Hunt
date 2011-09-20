<?php

/**
 * Contains logic for communicating with the Treasure Hunt database
 *
 * @package	model
 * @author	loganfranken
 */

/**
 * Handles operations against the Treasure Hunt database
 *
 * @package	model
 * @author	loganfranken
 */
class Model
{
	/**
	 * Distance (in feet) that a User has to be within to successfully dig up a treasure
	 * @var	integer
	 */
	const DIG_DISTANCE = 4;
	
	/**
	 * Distance (in feet) that a User has to be within to successfully find a treasure during
	 * a search
	 * @var	integer
	 */
	const SEARCH_DISTANCE = 30;

	/**
	 * Database host
	 * @var	string
	 */
	private static $dbHost = 'localhost';
	
	/**
	 * Database username
	 * @var	string
	 */
	private static $dbUser = 'DB_USER';
	
	/**
	 * Database password
	 * @var	string
	 */
	private static $dbPass = 'DB_PASSWORD';
	
	/**
	 * Database name
	 * @var	string
	 */
	private static $dbName = 'DB_NAME';
	
    /**
	 * Determines if a treasure is buried in the specified location
	 *
	 * @param	float	$latitude	Latitude of the location to look for treasure
	 * @param	float	$longitude	Longitude of the location to look for treasure
	 *
	 * @return	bool	True if a treasure is buried in the specified location. False otherwise
	 *
	 * @access	public
	 * @static
	 */
	public static function isTreasureBuried($latitude, $longitude)
	{
		$treasure = self::getTreasure($latitude, $longitude, self::DIG_DISTANCE);
		return ($treasure !== NULL);
	}
	
    /**
	 * Digs for treasure in the specified location. If a treasure is found, the treasure is
	 * returned and removed from that spot. Otherwise, NULL is returned
	 *
	 * @param	float	$latitude	Latitude of the location to dig for treasure
	 * @param	float	$longitude	Longitude of the location to dig for treasure
	 *
	 * @return	Treasure	The treasure, if a treasure is found in the specified location.
	 *						Otherwise, NULL is returned
	 *
	 * @access	public
	 * @static
	 */
	public static function digForTreasure($latitude, $longitude)
	{
		$treasure = self::getTreasure($latitude, $longitude, self::DIG_DISTANCE);
		
		// A treasure was found, so remove it from the database
		if($treasure !== NULL)
		{
			$data = self::executeNonQuery('delete_treasure', array($treasure->id));
		}
		
		return $treasure;
	}
	
	/**
	 * Searches for treasure around the specified location. If a nearby treasure is found, the
	 * treasure is returned. Otherwise, NULL is returned
	 *
	 * @param	float	$latitude	Latitude of the location to search for treasure
	 * @param	float	$longitude	Longitude of the location to search for treasure
	 *
	 * @return	Treasure	The treasure, if a treasure is found nearby the specified location.
	 *						Otherwise, NULL is returned
	 *
	 * @access	public
	 * @static
	 */
	public static function searchForTreasure($latitude, $longitude)
	{
		$treasure = self::getTreasure($latitude, $longitude, self::SEARCH_DISTANCE);	
		return $treasure;
	}
	
    /**
	 * Buries a treasure in the specified location
	 *
	 * @param	string	$itemName	Name of the treasure to bury
	 * @param	float	$latitude	Latitude of the location to bury the treasure
	 * @param	float	$longitude	Longitude of the location to bury the treasure
	 *
	 * @return	void
	 *
	 * @access	public
	 * @static
	 */
	public static function buryTreasure($itemName, $latitude, $longitude)
	{
		self::executeNonQuery('add_treasure', array($itemName, $latitude, $longitude));
	}
	
	/**
	 * Searches for treasure around the specified location within the specified distance. If a
	 * nearby treasure is found, the treasure is returned. Otherwise, NULL is returned
	 *
	 * @param	float	$latitude	Latitude of the location to search for treasure
	 * @param	float	$longitude	Longitude of the location to search for treasure
	 * @param	float	$distance	Search distance
	 *
	 * @return	Treasure	The treasure, if a treasure is found nearby the specified location
	 *						within the specified distance. Otherwise, NULL is returned
	 *
	 * @access	public
	 * @static
	 */
	public static function getTreasure($latitude, $longitude, $distance)
	{
		$data = self::executeQuery('get_treasure',
			array($latitude, $longitude, $distance));
			
		$treasure = self::mapTreasure($data);
		
		return $treasure;
	}
	
    /**
	 * Maps the results of a database query to a Treasure object
	 *
	 * @param	array	$data	Results of a database query
	 *
	 * @return	Treasure	Mapped Treasure object
	 *
	 * @access	private
	 * @static
	 */
	private static function mapTreasure($data)
	{
		$treasure = ($data)
			? new Treasure($data['treasure_id'], $data['name'],	
							$data['latitude'], $data['longitude'], $data['distance']) : NULL;
			
		return $treasure;
	}

	/**
	 * Executes a stored procedure that does not return results
	 *
	 * @param	string	$procName	Name of the stored procedure
	 * @param	array	$args		Arguments for the stored procedure
	 *
	 * @return	void
	 *
	 * @access	private
	 * @static
	 */
	private static function executeNonQuery($procName, $args)
	{
		$db = self::getConnection();
		$procArgs = self::buildArgs($db, $args);
		
		$db->query('CALL ' . $procName . '(' . $procArgs . ')');
	}
	
	/**
	 * Executes a stored procedure that returns results
	 *
	 * @param	string	$procName	Name of the stored procedure
	 * @param	array	$args		Arguments for the stored procedure
	 *
	 * @return	array	Results of the stored procedure
	 *
	 * @access	private
	 * @static
	 */
	private static function executeQuery($procName, $args)
	{
		$db = self::getConnection();
		$procArgs = self::buildArgs($db, $args);
		
		$result = $db->query('CALL ' . $procName . '(' . $procArgs . ')');
		
		// Credit: http://www.rvdavid.net/using-stored-procedures-mysqli-in-php-5/
		if($result)
		{
			$data = $result->fetch_assoc();
			
			$result->free();
			
			// Free results
			while ($db->next_result())
			{
				$result = $db->use_result();
				
				if ($result instanceof mysqli_result)
				{
					$result->free();
				}
			}
			
			return $data;
		}
		
		return NULL;
	}
	
	/**
	 * Returns a "connection" to the database
	 *
	 * @return	MySQLI	Object for communicating with the database
	 *
	 * @access	private
	 * @static
	 */
	private static function getConnection()
	{
		return new MySQLI(self::$dbHost, self::$dbUser, self::$dbPass, self::$dbName);
	}

	/**
	 * Builds an argument list for a stored procedure in MySQL, escaping the arguments, surrounding
	 * the arguments in single quotes, and delimiting the argument list with commas
	 *
	 * @param	MySQLI	$mysqli	MySQLI object (used for escaping parameters)
	 * @param	array	$args	Arguments for the stored procedure
	 *
	 * @return	string	List of arguments, prepared for stored procedure call
	 *
	 * @access	private
	 * @static
	 */	
	private static function buildArgs($mysqli, $args)
	{
		foreach ($args as $a)
		{
			$procArgs[] = "'" . $mysqli->real_escape_string($a) . "'";
		}
		
		return implode($procArgs, ',');
	}
}

?>