Treasure Hunt
=============

This is a simple game that I made as a demo of the [UCLA Mobile Web Framework](http://mwf.ucla.edu/). 
Users can bury a treasure in a spot based on their current location and then another User can dig
up the treasure from that spot.

## Installation

If you want to install the game on your own server, you just need to follow a few simple steps:

1.   **Create and Populate the Database**
       
	 Create the MySQL database that will store the Treasure Hunt table. This database will only
	 contain one table and three stored procedures (detailed in `treasure.sql`). The database user
	 will only need `EXECUTE` permissions on the database
	 
2.   **Update the Model Class**

	 Update the database values in the `Model` class (in `model.php`):
	 
	 * `$dbHost`: Database host (e.g.: `localhost`)
	 * `$dbUser`: Username of the database
	 * `$dbPass`: Password of the database user
	 * `$dbName`: Name of the database
	 
## Issues

First, this was made as a demo. If you want to take this and make a full-fledged application, then
you should replace out some of the back-end components (like the database connection logic) with
more robust libraries. Sometimes I like to reinvent the wheel for fun/experience on smaller
projects.

Second, the geolocation is finicky. You may not find treasures that other Users buried if your
phones are reporting different geolocation data. This could be potentially improved by increasing
the max distance for digging and searching (`DIG_DISTANCE` and `SEARCH_DISTANCE` in the `Model`
class in `model.php`)