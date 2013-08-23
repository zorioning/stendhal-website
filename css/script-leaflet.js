(function(){
	//----------------------------------------------------------------------------
	//                                 Atlas
	//----------------------------------------------------------------------------
/*
	var mapType, infowindow;
	function EuclideanProjection() {
		var EUCLIDEAN_RANGE = 4*256; // move markers outside the map area far out of the way
		this.pixelOrigin = new google.maps.Point(EUCLIDEAN_RANGE / 2, EUCLIDEAN_RANGE / 2);
		this.pixelsPerLonDegree = EUCLIDEAN_RANGE / 360;
		this.pixelsPerLonRadian = EUCLIDEAN_RANGE / (2 * Math.PI);
		this.scaleLat = 2;      // Height - multiplication scale factor
		this.scaleLng = 1;      // Width - multiplication scale factor
		this.offsetLat = 0;     // Height - direct offset +/-
		this.offsetLng = 0;     // Width - direct offset +/-
	}

	EuclideanProjection.prototype.fromLatLngToPoint = function (latLng, opt_point) {
		var point = opt_point || new google.maps.Point(0, 0);
		var origin = this.pixelOrigin;
		point.x = (origin.x + (latLng.lng() + this.offsetLng ) * this.scaleLng * this.pixelsPerLonDegree);
		point.y = (origin.y + (-1 * latLng.lat() + this.offsetLat ) * this.scaleLat * this.pixelsPerLonDegree);
		return point;
	};

	EuclideanProjection.prototype.fromPointToLatLng = function (point) {
		var me = this;
		var origin = me.pixelOrigin;
		var lng = (((point.x - origin.x) / me.pixelsPerLonDegree) / this.scaleLng) - this.offsetLng;
		var lat = ((-1 *( point.y - origin.y) / me.pixelsPerLonDegree) / this.scaleLat) - this.offsetLat;
		return new google.maps.LatLng(lat , lng, true);
	};

	function worldToLatLng(x, y) {
		var xw0 = 499616;
		var yw0 = 499744;
		var xwz = 501280;
		var ywz = 500896;

		var xl0 = 0;
		var yl0 = 0;
		var xlz = 208.15;
		var ylz = 144.2;

		var lx = (x - xw0) / (xwz - xw0) * (xlz - xl0) + xl0;
		var ly = (y - yw0) / (ywz - yw0) * (ylz - yl0) + yl0;
		return mapType.projection.fromPointToLatLng({x:lx, y:ly});
	}

	function initializeAtlas() {
		var tileUrlBase = $("#map_canvas").attr("data-tile-url-base");
		mapType.projection = new EuclideanProjection(); 

		var mapOptions = {
			backgroundColor: "#5f9860",
			center: worldToLatLng(parseInt($("#data-center").attr("data-x"), 10), parseInt($("#data-center").attr("data-y"), 10)),
			noClear: true,
			zoom: parseInt($("#data-center").attr("data-zoom"), 10),
			mapTypeControl: false,
			streetViewControl: false
		};

		if ($("#data-me").length > 0) {
			var me = new google.maps.Marker({
				position: worldToLatLng(parseInt($("#data-me").attr("data-x"), 10), parseInt($("#data-me").attr("data-y"), 10)),
				map: map, title:"Me",
				icon: "/images/mapmarker/me.png"
				});
			addClickEventToMarker(map, me, {
					name: "Me",
					title: "Me",
					description: "I am here at " + $("#data-me").attr("data-zone")
						+ " (" + $("#data-me").attr("data-local-x") + ", " + $("#data-me").attr("data-local-y") + ")",
					url: "/account/mycharacters.html"
				});
		}
		
	}
*/

	var mapX0 = -180, mapY0 = 90,  gameX0 = 499616, gameY0 = 499744;
	var mapXZ = 124,  mapYZ = -22, gameXZ = 501280, gameYZ = 500896;

	var gameXD = gameXZ - gameX0;
	var gameYD = gameYZ - gameY0;
	var mapXD = mapXZ = mapX0;
	var mapYD = mapYZ - mapY0;

	function gameToMap(point) {
		var x_ = point[0] - gameX0;
		var y_ = point[1] - gameY0;
		
		return [x_ * mapXD / gameXD + mapX0, y_ * mapYD / gameYD + mapY0];
	}

	function fromPointToLatLng(point) {
		var x = point[0] * (360 / 255) - 180; // (0 - 255) => (-180 - 180)
		var y = (255 - point[1]) * (180 / 255) - 90;    // (0 - 255) => (-90 - 90)
		return [y, x];
	}

	function worldToLatLng(point) {
		var x = point[0];
		var y = point[1];
		var xw0 = 499616;
		var yw0 = 499744;
		var xwz = 501280;
		var ywz = 500896;

		var xl0 = 0;
		var yl0 = 0;
		var xlz = 208.15;
		var ylz = 144.2;

		var xlz = 210;
		var ylz = 75;
		

		var lx = (x - xw0) / (xwz - xw0) * (xlz - xl0) + xl0;
		var ly = (y - yw0) / (ywz - yw0) * (ylz - yl0) + yl0;
//		console.log(x, y, "^=", lx, ly);
		return fromPointToLatLng([lx, ly]);
	}

	// http://www.netlobo.com/url_query_string_javascript.html
	function gup(name) {
		name = name.replace(/[\[]/, "\\\[").replace(/[\]]/,"\\\]");
		var regexS = "[\\?&]"+name+"=([^&#]*)";
		var regex = new RegExp( regexS );
		var results = regex.exec( window.location.href );
		if (results == null) {
			return "";
		}
		return results[1];
	}

	function initializeLeafletAtlas() {
/*		
		console.log(worldToLatLng([499616, 499744]));
		console.log(worldToLatLng([500000, 500000]));
		console.log(worldToLatLng([501280, 500896]));
*/

		var map = L.map('map_leaflet', {
			attributionControl: false
		});
		
		map.crs = L.CRS.Simple;
		map.setView(worldToLatLng([500000, 500000]), 3);
		
		var marker = L.marker(worldToLatLng([500000, 500000])).addTo(map);

		var pois = $.parseJSON($("#data-pois").attr("data-pois"));
		var wanted = decodeURI(gup("poi")).toLowerCase().split(",");
		var key;
		for (key in pois) {
			if (pois.hasOwnProperty(key)) {
				var poi = pois[key];
				if (($.inArray(poi.type.toLowerCase(), wanted) > -1)
					|| ($.inArray(poi.name.toLowerCase(), wanted) > -1)) {

					var marker = L.marker(
							worldToLatLng([poi.gx, poi.gy]), {
								icon: L.icon({iconUrl: "/images/mapmarker/" + poi.type + ".png"}),
								title: poi.name
							})
						.addTo(map)
						.bindPopup("<div style=\"max-width:400px\"><b><a target=\"_blank\" href=\""
							 + poi.url + "\">" 
							 + poi.title.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;")
							 + "</a></b><p>" + poi.description.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;") + "</p></div>");
				}
			}
		}
		

		
		L.tileLayer('https://stendhalgame.org/map/2/{z}-{x}-{y}.png', {
			attribution: '',
			minZoom: 2,
			maxZoom: 6,
			noWrap: true
		}).addTo(map);
	}



	//----------------------------------------------------------------------------
	//                                       init
	//----------------------------------------------------------------------------


	$().ready(function () {
		if (document.getElementById("map_leaflet") != null) {
			initializeLeafletAtlas();
		}
	});
}());
