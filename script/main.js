/**
 * @namespace Core namespace
 */
var TREASUREHUNT = {};

TREASUREHUNT.MIN_ACCURACY = 20;
TREASUREHUNT.GEO_TIMEOUT = 300000; // 5 minutes

/**
 * Creates a new Treasure Hunt View
 * @class	Manages UI operations for the Treasure Hunt interface
 * @param	{String}	statusSelector			DOM selector for the element that displays the
 *												status message
 * @param	{String}	itemNameInputSelector	DOM selector for the item name input
 */
TREASUREHUNT.View = function(statusSelector, itemNameInputSelector) {
	this.$statusElem = $(statusSelector);
	this.$itemNameInput = $(itemNameInputSelector);
};

/**
 * Displays a status message
 * @param	{String}	message		Status message
 * @param	{Boolean}	isError		Whether or not the status message is an error
 */
TREASUREHUNT.View.prototype.showStatus = function(message, isError) {
	this.$statusElem.text(message).toggleClass('error', isError).fadeIn();
};

/**
 * Hides the currently displayed status message
 */
TREASUREHUNT.View.prototype.hideStatus = function() {
	this.$statusElem.fadeOut().text();
};

/**
 * Gets the current value of the item name input
 */
TREASUREHUNT.View.prototype.getItemNameInputValue = function() {
	return this.$itemNameInput.val();
};

/**
 * Creates a new Treasure Hunt Model
 * @class	Communicates with the Treasure Hunt data access layer
 */
TREASUREHUNT.Model = function() {
};

/**
 * Requests a dig operation
 * @param	{Number}	latitude	Latitude of the location to dig
 * @param	{Number}	longitude	Longitude of the location to dig
 * @param	{Function}	callback	Function to call after submitting the dig request
 */
TREASUREHUNT.Model.prototype.requestDig = function(latitude, longitude, callback) {
	this.sendRequest('dig', null, latitude, longitude, callback);
};

/**
 * Requests a bury operation
 * @param	{String}	itemName	Name of the item to bury
 * @param	{Number}	latitude	Latitude of the location to bury the item
 * @param	{Number}	longitude	Longitude of the location to bury the item
 * @param	{Function}	callback	Function to call after submitting the dig request
 */
TREASUREHUNT.Model.prototype.requestBury = function(itemName, latitude, longitude, callback) {
	this.sendRequest('bury', itemName, latitude, longitude, callback);
};

/**
 * Requests a search operation
 * @param	{Number}	latitude	Latitude of the location to search
 * @param	{Number}	longitude	Longitude of the location to search
 * @param	{Function}	callback	Function to call after submitting the dig request
 */
TREASUREHUNT.Model.prototype.requestSearch = function(latitude, longitude, callback) {
	this.sendRequest('search', null, latitude, longitude, callback);
};

/**
 * Sends a generic Treasure Hunt request
 * @param	{String}	action		Action to request
 * @param	{String}	itemName	Name of the item to bury
 * @param	{Number}	latitude	Latitude of the location to search
 * @param	{Number}	longitude	Longitude of the location to search
 * @param	{Function}	callback	Function to call after submitting the dig request
 */
TREASUREHUNT.Model.prototype.sendRequest
	= function(action, itemName, latitude, longitude, callback) {
	
	$.post('lib/handler.php',
		{
			'action': action,
			'item-name': itemName,
			'latitude': latitude,
			'longitude': longitude
		},
		function(data) {
		
			callback && callback(data);
			
		}
	);
	
};

/**
 * Creates a new Treasure Hunt Controller
 * @class	Handles requests from the User, routing requests to the View and Model
 * @param	{View}	view	Treasure Hunt View
 * @param	{Model}	model	Treasure Hunt Mode
 */
TREASUREHUNT.Controller = function(view, model) {
	this.view = view;
	this.model = model;
};

/**
 * Handles a dig request from the User
 */
TREASUREHUNT.Controller.prototype.handleDigRequest = function() {

	var self = this;

	self.view.showStatus('Digging...', false);
	
	// Retrieve location
	mwf.touch.geolocation.getExactPosition(
		TREASUREHUNT.MIN_ACCURACY, TREASUREHUNT.GEO_TIMEOUT,
		function(pos) {
		
			// Send dig request
			self.model.requestDig(pos['latitude'], pos['longitude'],
				function(result) {
				
					// Request failed, display error message
					if(result.error)
					{
						self.view.showStatus(result.error, true);
						return;
					}
				
					// Request was successful
					if(result.data != null)
					{
						// An treasure was found
						self.view.showStatus('Congratulations! You found a '
												+ result.data.name, false);
					}
					else
					{
						// No treasure was found
						self.view.showStatus('No treasure found!', false);
					}
				}
			);
		
		},
		function(err) {
		
			// Retrieving location failed
			self.view.showStatus('Digging failed. Either geolocation isn\'t enabled or an '
									+ 'accurate position could not be determined', true);
			return;
			
		}
	);
	
};

/**
 * Handles a search request from the User
 */
TREASUREHUNT.Controller.prototype.handleSearchRequest = function() {

	var self = this;

	self.view.showStatus('Searching...', false);
	
	// Retrieve location
	mwf.touch.geolocation.getExactPosition(
		TREASUREHUNT.MIN_ACCURACY, TREASUREHUNT.GEO_TIMEOUT,
		function(pos) {
			
			// Send search request
			self.model.requestSearch(pos['latitude'], pos['longitude'],
				function(result) {
				
					// Request failed, display error message
					if(result.error)
					{
						self.view.showStatus(result.error, true);
						return;
					}
				
					// Request was successful
					if(result.data != null)
					{
						var distance = Math.ceil(result.data.distance);
						
						var formattedDistance = distance + ' feet away!';
						
						if(distance == 1)
						{
							formattedDistance = distance + ' foot away!';
						}
						
						if(distance == 0)
						{
							formattedDistance = 'right under your feet!';
						}

						// A treasure was found nearby
						self.view.showStatus('There\'s a treasure ' + formattedDistance, false);
					}
					else
					{
						// No treasure was found nearby
						self.view.showStatus('No treasure found nearby!', false);
					}
				
				}
			);
		
		},
		function(err) {
		
			// Retrieving location failed
			self.view.showStatus('Searching failed. Either geolocation isn\'t enabled or an '
									+ 'accurate position could not be determined', true);
			return;
			
		}
	);
	
};

/**
 * Handles a bury request from the User
 */
TREASUREHUNT.Controller.prototype.handleBuryRequest = function() {

	var self = this;

	// Validate
	var itemName = self.view.getItemNameInputValue();

	if(!itemName.match(/^[A-Za-z0-9][A-Za-z0-9 ]*$/, 'gim'))
	{
		self.view.showStatus('Invalid input. '
					+ 'Item name must contain only numbers, letters, and spaces', true);
		return;
	}

	if(itemName.length > 100)
	{
		self.view.showStatus('Item name must be less than 100 characters long', true);
		return;
	}
	
	self.view.showStatus('Burying...', false);
	
	// Retrieve location
	mwf.touch.geolocation.getExactPosition(
		TREASUREHUNT.MIN_ACCURACY, TREASUREHUNT.GEO_TIMEOUT,
		function(pos) {
		
			// Send bury request
			self.model.requestBury(itemName, pos['latitude'], pos['longitude'],
				function(result) {
				
					// Request failed, display error message
					if(result.error)
					{
						self.view.showStatus(result.error, true);
						return;
					}
				
					// Item was successfully buried
					self.view.showStatus('You successfully buried an item!', false);
				
				}
			);
			
		},
		function(err) {
		
			// Retrieving location failed
			self.view.showStatus('Burying failed. Either geolocation isn\'t enabled or an '
									+ 'accurate position could not be determined', true);
			return;
			
		}
	);
};

/**
 * Returns the position of the User within the specified accuracy
 * @param	{Number}	minAccuracy	Minimum accuracy (in meters) of the geolocation response
 * @param	{Number}	timeout		Maximum time (in milliseconds) that should be spent polling for
 *									the User's location. If the User's location can not be
 *									determined within this time, an error response will be returned
 * @param	{Function}	onSuccess	Function to call on successful retrieval of User's position
 * @param	{Function}	onError		Function to call if an error occurs
 */
mwf.touch.geolocation.getExactPosition = function(minAccuracy, timeout, onSuccess, onError) {
	
	var geo;
	
	switch(this.getType())
	{
		case 1:
			geo = navigator.geolocation;
			break;
		case 2:
			geo = google.gears.factory.create('beta.geolocation');
			break;
		default:
			onError && onError('No geolocation support available.');
			return;
	}

	var watchID = geo.watchPosition(
		function(position) {
		
			if(position.coords.accuracy <= minAccuracy) {
				navigator.geolocation.clearWatch(watchID);
				onSuccess && onSuccess({
					'latitude': position.coords.latitude,
					'longitude': position.coords.longitude,
					'accuracy': position.coords.accuracy
				});
			}
	
		},
		function() {
			onError && onError('Google Gears Geolocation failure.');
		},
		{
			enableHighAccuracy: true,
			timeout: timeout
		}
	);

	return true;
};

$(function() {

	var view = new TREASUREHUNT.View('p.status', '#item-name');
	var model = new TREASUREHUNT.Model();
	var controller = new TREASUREHUNT.Controller(view, model);

	var action = getUrlVar('action');
	
	switch(action)
	{
		case 'dig':
			controller.handleDigRequest();
			break;
			
		case 'bury':
			// No operation, bury operation handled by submit button
			break;
			
		default: // Default action is 'search'
			controller.handleSearchRequest();
			break;
	}
	
	$('#bury-form').submit(function(event) {
		event.preventDefault();
		controller.handleBuryRequest();
	});

	// Source: http://snipplr.com/view/799/
	function getUrlVar(varName) {
	
		var vars = [], hash;
		var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
 
		for(var i = 0; i < hashes.length; i++)
		{
			hash = hashes[i].split('=');
			vars.push(hash[0]);
			vars[hash[0]] = hash[1];
		}
		 
		return vars[varName];

	}
	
});