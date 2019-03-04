<?php

//
// Papurau Newydd Cymru
// Creu CSV o nifer o eiriau fesul blwyddyn
// Nid yw'r sgript yma yn enghraifft o raglen 'dda'!
// Mae e wedi cael ei greu yn ystod digwyddiad Hacathon Hanes, mis Mawrth 2019
//
// From the History Hackathon event. A rough script for querying Wales Newspapers Archive API - not reliable nor tidy!
//

echo "Blwyddyn,Nifer o eiriau,\n";
for($blwyddyn = 1804; $blwyddyn <= 1919; $blwyddyn++)
{
    $cyfeiriad = "http://papurapi.llgc.org.uk/?q=doc_type%3A%22Newspapers-article%22+AND+date_year%3A" . $blwyddyn . "&wt=json&indent=true&stats=true&stats.field=article_word_count";

    $json = @file_get_contents($cyfeiriad);

    // oedd methiant?
    if($json === FALSE) {
    echo "Methiant cysylltu â papurapi.llgc.org.uk :-(\n";
    exit;
    }

    // llwyddiant
    $stwffdata = json_decode($json);
    echo $blwyddyn . "," . ($stwffdata->stats->stats_fields->article_word_count->sum) . ",\n";
}
