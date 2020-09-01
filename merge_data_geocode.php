<?php
/**
 * token.php
 *
 * @package        Elezioni 2020
 * @author         Maurizio Mazzoneschi <mazzoneschi@lynxlab.com>         
 * @copyright      Copyright (c) 2020, Maurizio Mazzoneschi
 * @license        https://www.gnu.org/licenses/gpl-3.0.en.html GNU Public License v.3
 * @link           
 * @version		   0.1
 * @abstract	   Fonde il file dei risultati provenienti da Ministero Interno con il file
 * 				   dei confini (in geojson) della regione corrispondente
 * 
 * 				   legge da una directory i dati dello scrutinio
 *
 */


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

/*
 * ****************************
 * Fine
 */


