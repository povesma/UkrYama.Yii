function initialize(lat,lng) {
  var markers = [];
  var mapOptions = {
    zoom: 12,
    center: new google.maps.LatLng(lat,lng),
    mapTypeId: google.maps.MapTypeId.ROADMAP,
    disableDoubleClickZoom: true
  };
  var map = new google.maps.Map(document.getElementById('map-canvas'),
      mapOptions);
  var input = document.getElementById('target');
  var searchBox = new google.maps.places.SearchBox(input);
  google.maps.event.addListener(searchBox, 'places_changed', function() {
    var places = searchBox.getPlaces();

    for (var i = 0, marker; marker = markers[i]; i++) {
      marker.setMap(null);
    }
    markers = [];
    var bounds = new google.maps.LatLngBounds();
    for (var i = 0, place; place = places[i]; i++) {
      var image = {
        url: place.icon,
        size: new google.maps.Size(71, 71),
        origin: new google.maps.Point(0, 0),
        anchor: new google.maps.Point(17, 34),
        scaledSize: new google.maps.Size(25, 25)
      };
	if(place.types[1]=="political"){
		map.setZoom(12);
	}
	if(place.types=="route"){
		map.setZoom(14);
		var marker = new google.maps.Marker({
			map: map,
		        icon: image,
	        	title: place.name,
	        	position: place.geometry.location
		});
	      markers.push(marker);
	}
      bounds.extend(place.geometry.location);
    }
	map.setCenter(bounds.getCenter());
  });
  google.maps.event.addListener(map, 'bounds_changed', function() {
    var bounds = map.getBounds();
    searchBox.setBounds(bounds);
  });

var defectMarker = new google.maps.Marker({
	map: map,
	visible: false,
	draggable:true,
	icon: "http://newtest.ukryama.com/images/pmvvs.png"
});

  google.maps.event.addListener(map, 'dblclick', function(data) {
	defectMarker.setVisible(true);
	defectMarker.setPosition(data['latLng']);
	updateAddress();
  });
  var infowindow = new google.maps.InfoWindow();
	function updateAddress(){
		$.post("http://newtest.ukryama.com/event/GetAddress",{"lat":defectMarker.position['lat'](),"lng":defectMarker.position['lng']()},function(data){
			var resp = JSON.parse(data);

			var cord = new google.maps.LatLng(defectMarker.position['lat']()+0.003/Math.pow(2,(map.zoom-12)), defectMarker.position['lng']());
			var info = resp['results'][0].address_components;
			var streetNumber, route, locality, sublocality, administrative_area_level_2, administrative_area_level_1;
			for(i=0;i<info.length;i++){
				switch(info[i]['types'][0]){
					case "street_number":
						streetNumber=info[i]['long_name'];
					break;
					case "route":
						route=info[i]['long_name']+", ";
					break;

					case "sublocality":
						sublocality=info[i]['long_name']+", ";
					break;

					case "locality":
						locality=info[i]['long_name']+", ";
					break;

					case "administrative_area_level_2":
						administrative_area_level_2=info[i]['long_name']+", ";
					break;
					case "administrative_area_level_1":
						administrative_area_level_1=info[i]['long_name']+", ";
					break;

				}
			}
			administrative_area_level_1=(administrative_area_level_1=== undefined)?"1":administrative_area_level_1;
			sublocality=(sublocality=== undefined)?"":sublocality;
			route=(route=== undefined)?"":route;
			streetNumber=(streetNumber=== undefined)?"":streetNumber;
			address=administrative_area_level_1+sublocality+route+streetNumber;
			haddress.value=address;
			infowindow.setContent(address);
			infowindow.maxWidth=200;
			infowindow.setPosition(cord);
			infowindow.open(map);
			poslat.value=defectMarker.position['lat']();
			poslon.value=defectMarker.position['lng']();
		});
	}
}
$(window).keydown(function(e){
	if (e.keyCode==27){
		var c=document.getElementById('big_map');
		if(c){
			c.style.display='none';
			e.preventDefault();
		}
	}


});


