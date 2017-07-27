	var club = [
	  {
	    'name'  : 'Club 1',
	    'lat'   : '40.7927837',
	    'lng'   : '-74.10594130000002'
	  },
	  {
	    'name'  : 'Club 2',
	    'lat'   : '40.7127837',
	    'lng'   : '-74.231594130000002'
	  },
	  {
	    'name'  : 'Club 3',
	    'lat'   : '40.9127837',
	    'lng'   : '-74.20594130000002'
	  }
	];
	 

	 var countries = {
	  'au': {
	    center: {lat: -25.3, lng: 133.8},
	    zoom: 4
	  },
	  'br': {
	    center: {lat: -14.2, lng: -51.9},
	    zoom: 3
	  },
	  'ca': {
	    center: {lat: 62, lng: -110.0},
	    zoom: 3
	  },
	  'fr': {
	    center: {lat: 46.2, lng: 2.2},
	    zoom: 5
	  },
	  'de': {
	    center: {lat: 51.2, lng: 10.4},
	    zoom: 5
	  },
	  'mx': {
	    center: {lat: 23.6, lng: -102.5},
	    zoom: 4
	  },
	  'nz': {
	    center: {lat: -40.9, lng: 174.9},
	    zoom: 5
	  },
	  'it': {
	    center: {lat: 41.9, lng: 12.6},
	    zoom: 5
	  },
	  'za': {
	    center: {lat: -30.6, lng: 22.9},
	    zoom: 5
	  },
	  'es': {
	    center: {lat: 40.5, lng: -3.7},
	    zoom: 5
	  },
	  'pt': {
	    center: {lat: 39.4, lng: -8.2},
	    zoom: 6
	  },
	  'us': {
	    center: {lat: 37.1, lng: -95.7},
	    zoom: 5
	  },
	  'uk': {
	    center: {lat: 54.8, lng: -4.6},
	    zoom: 5
	  }
	};




	var infowindow, map, autocomplete;
	var countryRestrict = {'country': 'us'};
	var radius = 20000;
	var myMarker_array = [];


	function initMap() {
	  map = new google.maps.Map(document.getElementById('map'), {
	    center: countries['au'].center,
	    zoom: countries['au'].zoom,
	    scrollwheel: false,
	    mapTypeId: google.maps.MapTypeId.ROADMAP,
	    mapTypeControl: false,
	    // styles: styleArray
	  });

	  // Create the autocomplete object and associate it with the UI input control.
	  // Restrict the search to the default country, and to place type "cities".
	  autocomplete = new google.maps.places.Autocomplete((document.getElementById('autocomplete')),{
	    // types: ['(regions)'],
	    componentRestrictions: countryRestrict
	  });
	 
	  places = new google.maps.places.PlacesService(map);

	  autocomplete.addListener('place_changed', onPlaceChanged);

	  // Add a DOM event listener to react when the user selects a country.
	  document.getElementById('country').addEventListener('change', setAutocompleteCountry);

	  //initialize ifnfo window
	  infowindow = new google.maps.InfoWindow();

	  //initialize ifnfo marger(search marker)
	  marker = new google.maps.Marker({
	    map: map,
	    anchorPoint: new google.maps.Point(0, -29)
	  });
	}

	// initialize marker & infowindow from json(click)
	function addMyMarker(map, name, myLocation) {
	  var myMarker = new google.maps.Marker({
	    position: myLocation,
	    map: map,
	    animation: google.maps.Animation.DROP
	  });
	  
	  //add markers to array
	  myMarker_array.push(myMarker);

	  myMarker.addListener('click', function() {
	    if (typeof infowindow != 'undefined') infowindow.close();
	    infowindow = new google.maps.InfoWindow({
	      content: name
	    });
	    infowindow.open(map, myMarker);
	  });
	}

	// add my marker
	function myMarkersLoop(loc1) {
	  for (var x in club) {
	    var building = club[x];
	    var myLocation = new google.maps.LatLng(building.lat, building.lng);
	    //distance beetween search place and our markers
	    var d = google.maps.geometry.spherical.computeDistanceBetween( loc1, myLocation );
	    if  (d < radius) {
	      addMyMarker(map, building.name, myLocation);  
	    }
	  }
	}

	// remove markers from array
	function removeMarkers(){
	    for(i=0; i<myMarker_array.length; i++){
	        myMarker_array[i].setMap(null);
	    }
	}

	// When the user selects a city, get the place details for the city and
	// zoom the map in on the city.
	function onPlaceChanged() {
	  var place = autocomplete.getPlace();
	  infowindow.close();
	  marker.setVisible(false);
	  removeMarkers();

	  // If the place has a geometry, then present it on a map.
	  if (place.geometry) {
	    map.panTo(place.geometry.location);
	    map.setZoom(10);
	  } else {
	    document.getElementById('autocomplete').placeholder = 'Enter a city';
	  }

	  // add marker on
	  marker.setIcon(({
	    url: place.icon,
	    size: new google.maps.Size(71, 71),
	    origin: new google.maps.Point(0, 0),
	    anchor: new google.maps.Point(17, 34),
	    scaledSize: new google.maps.Size(35, 35)
	  }));
	  marker.setPosition(place.geometry.location);
	  marker.setVisible(true);
	  myMarkersLoop(place.geometry.location);
	  
	  // open info window on placeChange
	  var address = '';
	  if (place.address_components) {
	    address = [
	      (place.address_components[0] && place.address_components[0].short_name || ''),
	      (place.address_components[1] && place.address_components[1].short_name || ''),
	      (place.address_components[2] && place.address_components[2].short_name || '')
	    ].join(' ');
	  }
	  infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
	  infowindow.open(map, marker);

	  //radius circle
	  // radius_circle = new google.maps.Circle({
	  //   center: place.geometry.location,
	  //   radius: radius,
	  //   clickable: false,
	  //   map: map,
	  //   strokeColor: "#c4c4c4",
	  //   strokeOpacity: 0.35,
	  //   strokeWeight: 0,
	  //   fillColor: "gray",
	  //   fillOpacity: 0.35
	  // });

	}

	// Set the country restriction based on user input.
	// Also center and zoom the map on the given country.
	function setAutocompleteCountry() {
	  var country = document.getElementById('country').value;
	  // console.log(country);
	  if (country == 'all') {
	    autocomplete.setComponentRestrictions([]);
	    map.setCenter({lat: 15, lng: 0});
	    map.setZoom(2);
	  } else {
	    autocomplete.setComponentRestrictions({'country': country});
	    map.setCenter(countries[country].center);
	    map.setZoom(countries[country].zoom);
	  }
	  
	}
