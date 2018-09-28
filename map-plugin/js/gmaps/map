function initialize() {
	var hlat = parseFloat(helper.lat)||39.0000;
	var hlng = parseFloat(helper.lng)||22.0000;

	var myLatLng = new google.maps.LatLng(hlat,hlng);
	var mapOptions = {
	  center: myLatLng,
	  zoom: 5
	};
	var map = new google.maps.Map(document.getElementById('map-canvas'),
	    mapOptions);
	var marker = new google.maps.Marker({position: myLatLng, map: map, draggable: true});
	marker.setMap(map);

    google.maps.event.addListener(map, 'click', function(event) {
        placeMarker(event.latLng);
    });


	function placeMarker(location) {



	    if (marker == undefined){
	        marker = new google.maps.Marker({
	            position: location,
	            map: map,
	            animation: google.maps.Animation.DROP
	        });
	    }
	    else {
	        marker.setPosition(location);
	    }
	    map.setCenter(location);
	    //console.log(location.lat()+" "+location.lng());		// click debug
	    document.getElementById("latitude").value = location.lat();
	    document.getElementById("longitude").value = location.lng();
	}

}
google.maps.event.addDomListener(window, 'load', initialize);