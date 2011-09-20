--
-- Table structure for table `treasure`
--

DROP TABLE IF EXISTS `treasure`;
CREATE TABLE IF NOT EXISTS `treasure` (
  `treasure_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `latitude` decimal(10,6) NOT NULL,
  `longitude` decimal(10,6) NOT NULL,
  PRIMARY KEY (`treasure_id`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

DELIMITER $$
--
-- Procedures
--
CREATE PROCEDURE `add_treasure`(IN inName VARCHAR(100), IN inLatitude DECIMAL(10,6), IN inLongitude DECIMAL(10,6))
BEGIN

INSERT INTO treasure (name, latitude, longitude)
VALUES (inName, inLatitude, inLongitude);

END$$

CREATE PROCEDURE `delete_treasure`(IN inTreasureID INT)
BEGIN
	DELETE FROM `treasure`
	WHERE treasure_id = inTreasureID;
END$$

CREATE PROCEDURE `get_treasure`(IN inLatitude DECIMAL(10,6), IN inLongitude DECIMAL(10,6), IN inDistance DECIMAL(10,6))
BEGIN
	SELECT treasure_id, name, latitude, longitude, GREATEST(latDiff, longDiff) AS distance
	FROM
		(SELECT treasure_id, name, latitude, longitude,
		(364848 * ABS(treasure.latitude - inLatitude)) AS latDiff,
		(279840 * ABS(treasure.longitude - inLongitude)) AS longDiff
		FROM `treasure`
		HAVING (latDiff < inDistance) AND (longDiff < inDistance)
		LIMIT 1) AS result;
END$$

DELIMITER ;
