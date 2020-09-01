<?php

if (isset($_GET['reg']) && ($_GET['reg'] != null)) {
    $reg = $_GET['reg'];
} else {
	$reg = 'VENETO';
}

function dir_list($directory = FALSE) {
	$dirs= array();
	$files = array();
	if ($handle = opendir("./" . $directory))
	{
	  while ($file = readdir($handle))
	  {
		if (is_dir("./{$directory}/{$file}"))
		{
		  if ($file != "." & $file != "..") $dirs[] = $file;
		}
		else
		{
		  if ($file != "." & $file != "..") $files[] = $file;
		}
	  }
	}
	closedir($handle);
	return $files;
}

$fileScrutiniAr = dir_list("dati/json_scrutini/");

$fileGeo = 'dati/confini_ISTAT_per_regioni/'.$reg.'.json';

$file2write = 'dati/scrutinio_comunali_'.$reg.'.json';

$geoString = file_get_contents($fileGeo);
$jsonGeoObject = json_decode($geoString);

$jsonObjectNew = new stdClass();
$jsonObjectNew->type = "FeatureCollection";
$jsonObjectNew->features = [];
$jsonObjectNew->count = 0;
$count = 0;

/**
 * Struttura dati Out
 * 
 * Oggetto di oggetti comuni
 * ogni oggetto comune ha a sua volta n oggetti quanti sono i candidati 
 * 
 */

/*
Estrazione dati da scrutinio
*/
foreach ($fileScrutiniAr as $filenameIn) {
	$scrutinioString = file_get_contents("dati/json_scrutini/".$filenameIn);
	$jsonScrutinioObject = json_decode($scrutinioString);

	$singleComuneObject = $jsonScrutinioObject->int; 
	$descComune = $singleComuneObject->desc_com;
	$codComune = $singleComuneObject->cod_com;
	echo 'Comune: ' . $descComune . ' codice: '. $codComune .'<br>';  
	
	/**
	 * Ricerca del comune in geo per prendersi la geometria
	 */
	$comuneGeoAr = $jsonGeoObject->features;
	$confini = new stdClass();
//	foreach ($comuneGeoAr as $singleComuneGeo) {
	for ($i=0; $i < count($comuneGeoAr); $i++) { 
		$singleComuneGeo = $comuneGeoAr[$i]; 
		if (strtolower($singleComuneGeo->properties->COMUNE) == strtolower($descComune)) {
//			$confini = $singleComuneGeo->geometry; 
			if (property_exists($singleComuneGeo, 'geometry')) {
//				$jsonScrutinioObject->geometry = $confini; 
				$jsonGeoObject->features[$i]->int = $jsonScrutinioObject->int;
				$jsonGeoObject->features[$i]->note_din = $jsonScrutinioObject->note_din;
				$jsonGeoObject->features[$i]->cand = $jsonScrutinioObject->cand;
				echo 'Comune: ' . $descComune . ' codice: '. $singleComuneGeo->properties->COMUNE .' Indice: '.$i .'<br>';  
			}
		}
	}	
}



//var_dump($jsonScrutinioObject);

/**
 * Scrive oggetto in file.
 * 
 */
//header('Content-Type: application/json');

$dataJson = json_encode($jsonGeoObject); 
//var_dump($dataJson);
//echo $dataJson;
file_put_contents($file2write, $dataJson);
//file_put_contents($fileJs2write, 'statesData = '.$dataJson);

die();

/**
 * FINE
 */

/*
switch (json_last_error()) {
	case JSON_ERROR_NONE:
		echo ' - No errors';
		break;
	case JSON_ERROR_DEPTH:
		echo ' - Maximum stack depth exceeded';
		break;
	case JSON_ERROR_STATE_MISMATCH:
		echo ' - Underflow or the modes mismatch';
		break;
	case JSON_ERROR_CTRL_CHAR:
		echo ' - Unexpected control character found';
		break;
	case JSON_ERROR_SYNTAX:
		echo ' - Syntax error, malformed JSON';
		break;
	case JSON_ERROR_UTF8:
		echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
		break;
	default:
		echo ' - Unknown error';
		break;
}
*/
$dataAr = array_map('str_getcsv', file($filenameIn));
$header = array_shift($dataAr);

foreach ($dataAr as $stato) {
		$dataHA[] = array_combine($header, $stato);
}
//loop through jsonObject
foreach($jsonObject->features as $key => $feature) {
//	var_dump($feature);
	//check if there is data for this feature
	foreach ($dataHA as $stato) {
//		if ($feature->properties->su_a3 == $stato['su_a3']) {
//		if ($feature->properties->iso_a3 == $stato['iso_a3'] || $feature->properties->adm0_a3_us ==  $stato['iso_a3']) {
		if ($feature->properties->ISO_A3 == $stato['iso_a3']) {
			$feature->proprieta = new stdClass();
			$feature->proprieta->type = 'properties';
				foreach ($stato as $key => $valore) {
					$key=trim($key);
					$valore = trim($valore);
					$feature->proprieta->$key = $valore;
			}
/*
			$feature->properties->governo_statale = $stato['governo_statale'];
			$feature->properties->forma_governo = $stato['forma_governo'];
			$feature->proprieta->forma_governo = $stato['forma_governo'];
			$feature->proprieta->stato = $stato['stato'];
			$feature->proprieta->iso_a3 = $stato['iso_a3'];
//			print_r($feature->proprieta->iso_a3);
 * 
 */
			$jsonObjectNew->features[] = $feature;
			$count++;
			}
	}
	unset($feature->properties);
	$jsonObject->features[$key] = $feature;
	}
	$jsonObjectNew->count = $count;
	

//encode and output jsonObject
header('Content-Type: application/json');
$dataJson = json_encode($jsonObjectNew); // file contenente solo gli stati che hanno proprietà forma di governo
// $dataJson = json_encode($jsonObject); // file completo con aggiunta delle proprietà
echo $dataJson;
file_put_contents($file2write, $dataJson);
file_put_contents($fileJs2write, 'statesData = '.$dataJson);


/*
 * ****************************
 * Fine
 */


