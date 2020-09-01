<?php

/**
 * Codici ISTAT Regioni
 */
$RegioniAR[1] = 'PIEMONTE';
$RegioniAR[2] = 'VALLE D’AOSTA';
$RegioniAR[3] = 'LOMBARDIA';
$RegioniAR[4] = 'TRENTINO A. A.';
$RegioniAR[5] = 'VENETO';
$RegioniAR[6] = 'FIULI V. G.';
$RegioniAR[7] = 'LIGURIA';
$RegioniAR[8] = 'EMILIA ROMAGNA';
$RegioniAR[9] = 'TOSCANA';
$RegioniAR[10] = 'UMBRIA';
$RegioniAR[11] = 'MARCHE';
$RegioniAR[12] = 'LAZIO';
$RegioniAR[13] = 'ABRUZZI';
$RegioniAR[14] = 'MOLISE';
$RegioniAR[15] = 'CAMPANIA';
$RegioniAR[16] = 'PUGLIE';
$RegioniAR[17] = 'BASILICATA';
$RegioniAR[18] = 'CALABRIA';
$RegioniAR[19] = 'SICILIA';
$RegioniAR[20] = 'SARDEGNA';

$filenameIn = 'dati/confini_istat/comuni_WGS84.json';
$file2writePath = 'dati/confini_ISTAT_per_regioni/';
$file2writeExtension = '.json';

$geoString = file_get_contents($filenameIn);
$jsonGeoObject = json_decode($geoString);

// visualizzazione oggetto scrutinio completo
//var_dump($jsonScrutinioObject);
for ($i=1; $i <21; $i++) { 
	$jsonObjectNew[$i] = new stdClass();
	$jsonObjectNew[$i]->type = "FeatureCollection";
	$jsonObjectNew[$i]->cod_reg = $i;
	$jsonObjectNew[$i]->regione = $RegioniAR[$i];
	$jsonObjectNew[$i]->features = [];
}

/*
$jsonObjectNew = new stdClass();
$jsonObjectNew->type = "FeatureCollection";
$jsonObjectNew->features = [];
$jsonObjectNew->count = 0;
*/

$codiceRegione = 0;
$count = 0;

$confiniCommuni = $jsonGeoObject->features;
foreach ($confiniCommuni as $confiniCommuneSingolo) {
	$codReg = $confiniCommuneSingolo->properties->COD_REG;
	$jsonObjectNew[$codReg]->features[] = $confiniCommuneSingolo;
}

$totComuni = 0;
for ($i=1; $i<21; $i++) {
	//	header('Content-Type: application/json');
	$numComuni = count($jsonObjectNew[$i]->features);
	$totComuni += $numComuni;
	$jsonObjectNew[$i]->numeroComuni = $numComuni;
	$dataJson = json_encode($jsonObjectNew[$i]); 
	$fileOut = $file2writePath.$RegioniAR[$i].$file2writeExtension;
	file_put_contents($fileOut, $dataJson);
	echo '<br>Ho scritto: '.$fileOut . ' numero comuni: '. $numComuni;  //$dataJson;
}

echo '<br>Totale comuni: '.$totComuni;  //$dataJson;
die();


/**
 * Versione che scrive quando cambia il COD_REG 
foreach ($confiniCommuni as $confiniCommuneSingolo) {
	if ($codiceRegione == $confiniCommuneSingolo->properties->COD_REG){
		$jsonObjectNew->features[] = $confiniCommuneSingolo;
	} else {
		if ($codiceRegione <> 0) {
			$fileOut = $file2writePath.$noneRegione.$file2writeExtension;
			scriviComune($noneRegione,$jsonObjectNew, $fileOut);
			$jsonObjectNew = nuovoOggetto();
		}
		$codiceRegione = $confiniCommuneSingolo->properties->COD_REG;
		$noneRegione = $RegioniAR[$codiceRegione];
		$jsonObjectNew->features[] = $confiniCommuneSingolo;
	}
}	

function nuovoOggetto(){
	$jsonObjectNew = new stdClass();
	$jsonObjectNew->type = "FeatureCollection";
	$jsonObjectNew->features = [];
	return $jsonObjectNew;
}

function scriviComune($nomeRegione,$jsonObjectNew, $fileOut) {
	//	header('Content-Type: application/json');
		$dataJson = json_encode($jsonObjectNew); 
		echo $dataJson;
		file_put_contents($fileOut, $dataJson);
//		file_put_contents($fileJs2write, 'statesData = '.$dataJson);
}
 */


/*
 * ****************************
 * Fine
 */


