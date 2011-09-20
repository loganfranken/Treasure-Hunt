<?php

/**
 * Contains core business objects
 *
 * @package	core
 * @author	loganfranken
 */

/**
 * A buried treasure
 *
 * @package	core
 * @author	loganfranken
 */

class Treasure
{
	/**
	 * Unique ID of the buried item
	 * @var	integer
	 */
	public $id;

	/**
	 * Name of the buried item
	 * @var	string
	 */
	public $name;
	
	/**
	 * Latitude of the buried item's location
	 * @var	float
	 */
	public $latitude;
	
	/**
	 * User's distance from the treasure
	 * @var	float
	 */
	public $distance;
	
	/**
	 * Longitude of the buried item's location
	 * @var	float
	 */
	public $longitude;
	
	public function __construct($id, $name, $latitude, $longitude, $distance)
	{
		$this->id = $id;
		$this->name = $name;
		$this->latitude = $latitude;
		$this->longitude = $longitude;
		$this->distance = $distance;
	}
}

?>