var destino;
var origen;
var travel_mode;
var markerOrigen;

function getIcon(letra_color){
	var pinColor = letra_color;
  /**  var pinImage = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_icon_whitshadow&chld=%E2%80%A2|" + pinColor,
        new google.maps.Size(21, 34),
        new google.maps.Point(0,0),
        new google.maps.Point(10, 34)
    );**/
   var pinImage = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter_withshadow&chld="+pinColor+"|000000");
    return pinImage;
}



function setCoordsDestination(lat,lng)
{
	destino = new google.maps.LatLng(lat,lng);
	drawMap(destino);
}

function drawMap(destino) {
	//destino = new google.maps.LatLng(lat, lng);
	directionsDisplay = new google.maps.DirectionsRenderer(
		{
		suppressMarkers:true
		
		}
	);
	var myOptions = {
		zoom : 17,
		center : destino,
		//center : new google.maps.LatLng(19.541829, -96.926688),
		mapTypeId : google.maps.MapTypeId.ROADMAP,
		overviewMapControl : true
	};
		
	var map = new google.maps.Map(document.getElementById("mapa"), myOptions);
	directionsDisplay.setMap(map);
	
	var markerDestino = new google.maps.Marker({
					position : destino,
					map : map,
					icon: getIcon("D|65ba4a")
					
				});
	
	setCoordsOrigin(destino);
	
	
	
}

function alternateCoordsOrigin(){
	if(google.loader.ClientLocation) {
		latitude = google.loader.ClientLocation.latitude;
		longitude = google.loader.ClientLocation.longitude;
		latlng = new google.maps.LatLng(latitude, longitude);
		return latlng;
		}
		else{
			alert('No fue posible detectar su ubicacion');
		}
	}



function setCoordsOrigin(destino){   
	travel_mode = google.maps.DirectionsTravelMode.DRIVING;
	if(navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function(position) {
			
			origen = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
			drawRoute(destino,origen,travel_mode);
		},
		function errorCallback(error){
			origen = alternateCoordsOrigin();
			drawRoute(destino,origen,travel_mode);
		});
	}else{
		 origen = alternateCoordsOrigin();
		 drawRoute(destino,origen,travel_mode);	
	}
}

function drawRoute(destino,origen,travel_mode){
		
	//console.log("draw route");
	map = directionsDisplay.getMap();
	if (typeof markerOrigen == "undefined"){
		markerOrigen = new google.maps.Marker({
					position : origen,
					map : map,
					draggable:true,
					icon:getIcon("I|12869F"),
					title:"Usted está aquí"
		});	
		google.maps.event.addListener(markerOrigen, 'dragend', markerDraged);
	
	}
	var directionsService = new google.maps.DirectionsService();
	var request = {
		origin : origen,
		destination : destino,
		travelMode : travel_mode
	};
	
	directionsService.route(request, function(response, status) {
		if(status == google.maps.DirectionsStatus.OK) {
			directionsDisplay.setDirections(response);
			jQuery('#distance').html(response.routes[0].legs[0].distance.text);
     		jQuery('#time').html(response.routes[0].legs[0].duration.text);
		}
	});
	
	
	
	
}

function markerDraged(){
	//console.log('draged');
	origen = markerOrigen.getPosition();
	markerOrigen.setPosition(origen);
	drawRoute(destino, origen,travel_mode);
}


		
	
jQuery(document).ready(function() {	
	jQuery('#route_type').change(function() {
			travel_mode = jQuery("#route_type").val();
			if(travel_mode == "driving")
				travel_mode = google.maps.DirectionsTravelMode.DRIVING;
			else if(travel_mode == "walking")
				travel_mode = google.maps.DirectionsTravelMode.WALKING;
			drawRoute(destino, origen, travel_mode);
	
		});
	
     function updateInfo(distance,time){
     	jQuery('#distance').html(distance);
     	jQuery('#time').html(time);
     }
	 		
});	

/**

**/



	








/**
function ruta(destino) {
	origen = getCoordsOrigen();
	var directionsService = new google.maps.DirectionsService();
	//inicio = origen;
	//fin = destino;
	var request = {
		origin : origen,
		destination : destino,
		jQueryMode : google.maps.DirectionsjQueryMode.DRIVING
		};
	directionsService.route(request, function(response, status) {
		if(status == google.maps.DirectionsStatus.OK) {
			directionsDisplay.setDirections(response);
		}
	});
		
}
**/