var map = L.map('map').setView([46, 11,5], 8);
map.zoomControl.setPosition('topright');
var info = L.control({position: 'topleft'});

var approfondimento = L.control({position: 'bottomleft'});

var jsonComuni = $.getJSON({'url': "dati/scrutinio_comunali_VENETO.json", 'async': false})
//The next line of code will filter out all the unwanted data from the object.
jsonComuni = JSON.parse(jsonComuni.responseText); 

function initMap() {

/**
 * 
	L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoiZ3JhZmZpb20iLCJhIjoiY2lxdGo0bGI0MDAxc2hzbTNqd2JraTA1ZSJ9.ttTiVF6_toROYayxj0UtnA', {
		maxZoom: 18,
		attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a>',
		id: 'mapbox.light'
	}).addTo(map);
 */

//	var OSM_layer = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png',
//		{attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> | Developed by graffio'
//	}).addTo(map);

	L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
		attribution: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ',
		maxZoom: 16
	}).addTo(map);

	geojson = L.geoJson(jsonComuni, {
		style: style,
		onEachFeature: onEachFeature
	}).addTo(map);

	// controllo che mostra i dati del comune
	info.onAdd = function (map) {
		this._div = L.DomUtil.create('div', 'info');
		this.update();
		return this._div;
	};	

	info.update = function (feature) {
		if (feature) {
			this._div.innerHTML = '<h4>Dati comune di ' +  
				'<b>' + feature.properties.COMUNE +'</b></h4><br />' + 'Percentuale votanti: ' + feature.int.perc_vot +'%<br>' +
				' Risultati: <br>' + votiCandidati(feature.cand)
		} else {
			this._div.innerHTML = '<h4>Vai con il mouse sui comuni colorati</h4>'
	
		}
	};
	info.addTo(map);
	
}

function votiCandidati(risultati) {
	var risultatiReturn = ''
	risultati.forEach(function(item) {
		if (item.voti_1t) {
			risultatiReturn = risultatiReturn + item.cogn + ' ' + item.nome + ', Voti: ' + item.voti_1t + ' ' + parseInt(item.perc_1t).toFixed(2) + '%<br>'   
//			risultatiReturn = risultatiReturn + item.cogn + ' ' + item.nome + ', Voti: ' + item.voti_1t + ' ' + Math.round(parseInt(item.perc_1t)) + '%<br>'   
		} else {
			risultatiReturn = risultatiReturn + item.cogn + ' ' + item.nome + ', Voti: ' + item.voti + ' ' + parseInt(item.perc).toFixed(2) + '%<br>'   
//			risultatiReturn = risultatiReturn + item.cogn + ' ' + item.nome + ', Voti: ' + item.voti + ' ' + Math.round(parseInt(item.perc)) + '%<br>'   
		}
	});
	return risultatiReturn
}

function style(feature) {
	var perc = 0
	var Opacity = 0
	var colorFill = 'white'
	if ('int' in feature !== false &&  feature.int !== null) {
//		alert (feature.cand[0].cogn+' '+feature.cand[0].nome+ ' '+feature.cand[0].perc)	
		perc = parseFloat(feature.cand[0])
		Opacity = (perc + 35) /100
		colorFill = getColor(feature.int.perc_vot)
	}
	return {
			weight: 1,
			opacity: 1,
			color: '#1b3547',
			dashArray: '1',
			fillOpacity: Opacity,
			fillColor: colorFill
		};
}

// get color depending on forma_governo value
function getColor(d) {
	return '#F3B224'
	/**
	return (d.listeRisultati[0].descrizione.toLowerCase() == 'centro destra') ? '#045992':
		(d.listeRisultati[0].descrizione.toLowerCase() == 'movimento 5 stelle') ? '#F9C134':
		(d.listeRisultati[0].descrizione.toLowerCase() == 'centro sinistra') ? '#D23933':
	*/	
		/*
		(-1 !== d.toLowerCase().indexOf('lazio')) ? '#F9C134':
		*/
//											'';
}


function highlightFeature(e) {
	var layer = e.target;

	layer.setStyle({
		weight: 3,
		color: '#666',
		dashArray: '',
//		fillOpacity: 0.7
	});

	if (!L.Browser.ie && !L.Browser.opera) {
		layer.bringToFront();
	}

//	info.update(layer.feature);
	infoFuori(layer.feature);
//		info.update(layer.feature.properties,layer.feature.risultati);
}

var geojson;
function resetHighlight(e) {
	geojson.resetStyle(e.target);
//	info.update();
	infoFuori();
//			approfondimento.update();
}

function zoomToFeature(e) {
	map.fitBounds(e.target.getBounds());
}

function onEachFeature(feature, layer) {
	if (feature.int) {
//			alert(feature.int.desc_com)
		layer.on({
			mouseover: highlightFeature,
			mouseout: resetHighlight,
//					click: zoomToFeature
			click: moreInfo
		});
	} 
}

function moreInfo(e) {
	var layer = e.target;

	layer.setStyle({
		weight: 5,
		color: '#666',
		dashArray: '',
		fillOpacity: 0.7
	});

	if (!L.Browser.ie && !L.Browser.opera) {
		layer.bringToFront();
	}
	alert ("link a pagina dettagli tabellari voti del comune di: " + layer.feature.properties.COMUNE)
}	

function infoFuori(feature) {
	if (feature) {
		$("#titolo").html('<h4>Dati comune di ' + '<b>' + feature.properties.COMUNE +'</b></h4>')
		$("#infoFuori").html('Percentuale votanti: ' + feature.int.perc_vot +'%<br>' +
		' <b>Risultati</b> <br>'+'<table id="tabellaDati"></table>')

/*		$("div.colDx").html('<h4>Dati comune di ' +  
		'<b>' + feature.properties.COMUNE +'</b></h4><br />' + 'Percentuale votanti: ' + feature.int.perc_vot +'%<br>' +
		' Risultati: <br>')
*/
		 votiCandidatiDT(feature.cand)

	} else {
		$("#titolo").html('<h4>Vai con il mouse sui comuni colorati</h4>')
		/*
		if ( $.fn.dataTable.isDataTable( '#tabellaDati' ) ) {
			var table = $('#example').DataTable();
			table.destroy();
		}
		*/
		$("#infoFuori").html('<table id="tabellaDati"></table>')
	}
}


function votiCandidatiDT(risultati) {
	var risultatiReturnAr = new Array
	risultati.forEach(function(item) {
		if (item.voti_1t) {
			risultatiReturnAr.push([item.cogn + ' ' + item.nome , item.voti_1t, parseInt(item.perc_1t).toFixed(2)+'%'])   
//			risultatiReturn = risultatiReturn + item.cogn + ' ' + item.nome + ', Voti: ' + item.voti_1t + ' ' + Math.round(parseInt(item.perc_1t)) + '%<br>'   
		} else {
//			risultatiReturn = risultatiReturn + item.cogn + ' ' + item.nome + ', Voti: ' + item.voti + ' ' + parseInt(item.perc).toFixed(2) + '%<br>'   
			risultatiReturnAr.push([item.cogn + ' ' + item.nome , item.voti, parseInt(item.perc).toFixed(2)+'%'])   
		}
	});
	if ( $.fn.dataTable.isDataTable( '#tabellaDati' ) ) {
		table = $('#example').DataTable();
		table
			.clear()
			.rows.add(risultatiReturnAr)
			.draw();

	} else {
		$('#tabellaDati').DataTable( {
			data: risultatiReturnAr,
			columns: [
				{ title: "Candidato" },
				{ title: "Voti", "sClass": "numericCol"},
				{ title: "Percentuale" },
			],
			"order": [[ 1, "desc" ]],
			"paging":   false,
			"ordering": false,
			"info":     false,
			"searching": false
		} );	
	
	}
}