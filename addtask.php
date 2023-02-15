<?php
require_once '../vendor/autoload.php';
//on ouvre le fichier json
$json = json_decode(file_get_contents('../deck.json'), true);
try {
    $stacks = exec('/home/debian/docker/applications/deck/get_stacks.sh' . ' 27');
} catch (Exception $e) {
    exit('Exception reçue : ' . $e->getMessage());
}
print_r($stacks);
$question = ['titre' => '', 'description' => '', 'col' => "113,129,105,119,125", 'jour' => "monday,thursday", 'heure' => 9, 'position' => "bottom"];
foreach ($question as $q => $def) {
    $reponse = readline("$q ($def)" . ': ');
    $rep[$q] = $reponse == '' ? $def : $reponse;
}
$titre = $rep['titre'];
unset($rep['titre']);
$json['create'][$titre] = $rep;
file_put_contents('../deck.json', json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
