<?php
require_once '../vendor/autoload.php';
require_once 'getstacks.php';
//on ouvre le fichier json
$json = json_decode(file_get_contents('../deck.json'), true);

$question = ['titre' => '', 'colstart' => $cols, 'colend' => $cols, 'jour' => "monday,thursday", 'heure' => 9];
foreach ($question as $q => $def) {
    $reponse = readline("$q ($def)" . ': ');
    $rep[$q] = $reponse == '' ? $def : $reponse;
}
$titre = $rep['titre'];
unset($rep['titre']);
$json['move'][$titre] = $rep;
file_put_contents('../deck.json', json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
