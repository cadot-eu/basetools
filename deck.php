<?php
require_once '../vendor/autoload.php';
//on ouvre le fichier json
$json = json_decode(file_get_contents('../deck.json'), true);
//boucle sur les actions
foreach ($json as $action => $contents) {
    foreach ($contents as $titre => $value) {
        //vérification de la date et du jour
        if (isset($value['heure'])) if (intval(date('H')) >= $value['heure']) echo $titre;
        if (isset($value['heure']) && isset($value['jour']) && strtolower(date('l')) == $value['jour'] and intval(date('H')) >= $value['heure']) {
            switch ($action) {
                case 'create':
                    #arguments: titre, description,col,ordre,archived,deleted
                    $value['ordre'] = 5000;
                    if (isset($value['position']) && $value['position'] == 'top')
                        $value['ordre'] = -1;
                    try {
                        exec('/home/debian/docker/applications/create_card.sh' . ' "' . $titre . '" "' . $value['description'] . '" ' . $value['col'] . ' ' . $value['ordre']);
                    } catch (Exception $e) {
                        exit('Exception reçue : ' . $e->getMessage());
                    }
                    break;
                case 'move':
                    break;
                case 'delete':
                    try {
                        exec('/home/debian/docker/applications/delete_card.sh' . ' "' . $titre . '" ' . $value['col']);
                    } catch (Exception $e) {
                        exit('Exception reçue : ' . $e->getMessage());
                    }
                    break;
            }
            unset($json[$action][$titre]);
            file_put_contents('../deck.json', json_encode($json));
        }
    }
}
