Prototipo elezioni comunali 2020
==========
Si tratta di un'insieme di script in forma prototipale che realizzano l'intero flusso per aggiornare i risultati elettorali dei comuni di una regione.

Pipeline
----------
1. una volta: divisione dei confini in formato geojson (forniti da ISTAT) in confini regionali 
2. scarimento del file risultati tramite le API del Ministero (script mancante)
3. merge dei dati scaricati dal ministero all'interno del file dei confini
4. visualizzazione della mappa regionale interrativa che mostra i dati dei risultati comunali

REQUIREMENTS
--------------
- jquery
- datatables jquery
- leaflet

ESECUZIONE
--------------
### divisione dei confini
Invocare **dividi_confini_comuni_x_regioni.php**

Va eseguito una sola volta. 

### Merge dei dati scaricati dal Ministeri con i confini
Invocare **merge_data_geocode.php?reg=REGIONE** (ES.: merge_data_geocode.php?reg=VENETO) 

Note: al momento il match tra il comune del file dei risultati con il comune nel file dei confini viene fatto sul nome. 

### Visualizzazione mappa
Invocare **scrutini_comunali.html**

Note: La mappa in questione visualizza sempre i dati relativi al Veneto. 

[Visualizzazione dell'esempio ](https://www.lynxlab.com/staff/graffio/public/mappe_elezioni_comunali/)

