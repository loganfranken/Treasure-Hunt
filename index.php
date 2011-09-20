<?php

/**
 * Main page
 *
 * @package	view
 * @author	loganfranken
 */

require_once('lib/utility.php');

$action = Request::getUrlVar('action');

switch($action)
{
	case 'bury':
		$section = 'Bury';
		break;
		
	case 'dig':
		$section = 'Dig';
		break;
		
	default: // 'Search' is the default action
		$action = 'search';
		$section = 'Search';
		break;
}

?>

<!DOCTYPE html>
<html lang="en-us">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="height=device-height,width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=no;" />
	<meta name="format-detection" content="telephone=no" />
	
	<title>Treasure Hunt &raquo; <?php echo $section; ?></title>
	
	<link rel="stylesheet" href="http://m.ucla.edu/assets/css.php" type="text/css">
	<link rel="stylesheet" href="style/main.css" type="text/css">
	
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
	<script type="text/javascript" src="http://m.ucla.edu/assets/js.php?no_ga&amp;no_icon&amp;standard_libs=geolocation"></script>
	<script type="text/javascript" src="script/main.js"></script>
</head>
<body>

	<h1 id="header">
		<span><a href="index.html">Treasure Hunt</a></span> 
	</h1>
	
<?php if($action === 'bury'): ?>

	<form id="bury-form" class="form-full form-padded">
		<h1 class="light form-first">Bury Treasure</h1>
		<p class="status center"></p>
		<label for="item-name">Name of the Item</label>
		<input type="text" name="item-name" id="item-name" />
		<input type="submit" name="submit-btn" class="form-last" value="Bury Treasure" />
	</form>

<?php elseif($action === 'dig'): ?>

	<div class="content-full content-padded">
		<h1 class="content-first light">Dig for Treasure</h1> 
		<div class="content-last center">
			<p class="status center"></p>
		</div>
	</div>

<?php elseif($action === 'search'): ?>

	<div class="content-full content-padded">
		<h1 class="content-first light">Nearby Treasure</h1> 
		<div class="content-last center">
			<p class="status center"></p>
		</div>
	</div>

<?php endif; ?>



	<a href="index.php?action=dig" class="button-full button-padded">Dig</a>
	<a href="index.php?action=search" class="button-full button-padded">Search</a>
	<a href="index.php?action=bury" class="button-full button-padded">Bury</a>

</body>
</html>