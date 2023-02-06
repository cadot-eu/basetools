<?php
require_once '../vendor/autoload.php';
//on ouvre le fichier json
$json = json_decode(file_get_contents('../deck.json'), true);
$question = ['titre' => '', 'description' => '', 'col' => 0, 'jour' => "monday", 'heure' => 9, 'position' => "bottom"];
foreach ($question as $q => $def) {
    $reponse = readline("$q ($def)" . ': ');
    $rep[$q] = $reponse == '' ? $def : $reponse;
}
$titre = $rep['titre'];
unset($rep['titre']);
$json['create'][$titre] = $rep;
file_put_contents('../deck.json', json_encode($json));
