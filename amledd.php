<?php

//
// Papurau Newydd Cymru
// Cyfrif defnydd o eiriau mewn papurau newydd, e.e. Eisteddfod, Dic Sion Dafydd, Father Christmas, Treganna
// Nid yw'r sgript yma yn enghraifft o raglen 'dda'!
// Mae e wedi cael ei greu yn ystod digwyddiad Hacathon Hanes, mis Mawrth 2019
//


// full_text:"" AND doc_type: "Newspapers-article" AND category:"Advertising"

$hyddarn = 200; // pa mor hir yw'r dyfyniadau o bapurau newydd (mewn symbolau)

// llinell orchymyn neu weinydd gwe
if(php_sapi_name() == 'cli') {
	$line_break = PHP_EOL;
} else {
	$line_break = '<br>';
}

//article_word_count:[* to 100]
// chwiliad gorau
// full_text:"gwellhad" AND doc_type: "Newspapers-article" AND category:"Advertising" AND article_word_count:[* TO 400]

// full_text:"Father Christmas" AND doc_type: "Newspapers-article" AND rows:15037357

// gorau
//$term_plaen = "Father Christmas";
//$term = "Father+Christmas";

if(isset($argv[1]))
	$term_plaen = $argv[1];
else {
    echo "\nDefnydd:\n\n\tphp " . $argv[0] . " [term] [nifer o resi]\n\n";
	echo "[term] Eich chwiliad fel testun\n";
	echo "[nifer o resi] Mae modd defnyddio hyd at 15037357 o resi. Os nad ydych chi'n rhoi nifer o resi bydd y system yn defnyddio 15037357 rhes fel prawf.\n\n";
    exit;
}
$term = urlencode($term_plaen);

if(isset($argv[2]))
	$rhesi = $argv[2];
else {
    $rhesi = 15037357;
}

//$rhesi = 15037357;

$stwffurl = "http://hacathon.lan:8983/solr/papur/select?q=full_text%3A%22" . $term . "%22+AND+doc_type%3A+%22Newspapers-article%22&rows=" . $rhesi ."&wt=json&indent=true";
//$stwffurl = "http://hacathon.lan:8983/solr/papur/select?q=full_text%3A%22" . $term . "%22+AND+doc_type%3A+%22Newspapers-article%22&rows=15037357&wt=json&indent=true";
//$stwffurl = "http://hacathon.lan:8983/solr/papur/select?q=full_text%3A%22" . $term . "%22+AND+doc_type%3A+%22Newspapers-article%22&rows=2014228&wt=json&indent=true";
//gwellhad

// syml
//$stwffurl ="http://hacathon.lan:8983/solr/papur/select?q=full_text%3A%22gwellhad%22+AND+doc_type%3A+%22Newspapers-article%22+AND+category%3A%22Advertising%22rows=2014228%22&wt=json&indent=true";

//$stwffurl = "http://hacathon.lan:8983/solr/papur/select?q=%0Adoc_type%3A+%22Newspapers-article%22+AND+category%3A%22Advertising%22&wt=json&indent=true";

$stwffjson = file_get_contents($stwffurl);
	
// oedd methiant?
if($stwffjson === FALSE) {
echo "methiant cysylltu â hacathon.lan :-(" . $line_break;
exit;
}

// llwyddiant
$stwffdata = json_decode($stwffjson);

//fersiwn lleol $stwffdata = file_get_contents($solr_lleol);
//var_dump($stwffdata->response->docs);

$blynyddoedd = array();
$teitlau = array();
$defnydd = array();
for($i = 1804; $i <= 1919; $i++) {
    $blynyddoedd[$i] = 0;
    $defnydd[$i] = 0;
}

foreach ($stwffdata->response->docs as &$eitem) {
    $teitl = $eitem->collection_title;
    $dyddiad = $eitem->date;
    $blwyddyn = substr($dyddiad, 0, 4);
    $testun = $eitem->full_text;

    $blynyddoedd[$blwyddyn]++;
    
    $pos = strpos($testun, $term_plaen);
    
    if($pos + $hyddarn >= strlen($testun))
        $nifer_o_symbolau = (strlen($testun) - $pos) -1;
    else
        $nifer_o_symbolau = $hyddarn;
        
    $defnydd[$blwyddyn] = $testun; //substr($testun, $pos, $nifer_o_symbolau);
    //$teitlau[$teitl]++;
    //echo $blwyddyn . "\n";
    //echo $teitl . $dyddiad . $nifer_o_eiriau . $testun;
    //blynyddoedd
}

echo "Blwyddyn,Nifer,Dyfyniad fel enghraifft\n";
for($i = 1804; $i <= 1919; $i++)
{
    //$canran = $blynyddoedd[$blwyddyn] / $nifer_o_gyhoeddiadau;
    echo $i . "," . $blynyddoedd[$i] . ",\"" . glanhauDyfyniad($defnydd[$i]) . "\"\n"; //. $canran 
}

function glanhauDyfyniad($testun)
{
    return preg_replace('/[^a-z0-9]/i', ' ', $testun);
}
