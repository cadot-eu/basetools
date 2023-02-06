<?php
require_once '../vendor/autoload.php';
//on ouvre le fichier json
$json = json_decode(file_get_contents('../deck.json'), true);
//boucle sur les actions
foreach ($json as $action => $contents) {
    foreach ($contents as $titre => $value) {
        $run = '';
        //vérification de la date et du jour
        if (isset($value['heure']) && isset($value['jour']) && strtolower(date('l')) == $value['jour'] and intval(date('H')) >= intval($value['heure'])) {
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
                    $run = 'create';
                    break;
                case 'move':
                    $run = 'move';
                    break;
                case 'delete':
                    try {
                        exec('/home/debian/docker/applications/delete_card.sh' . ' "' . $titre . '" ' . $value['col']);
                    } catch (Exception $e) {
                        exit('Exception reçue : ' . $e->getMessage());
                    }
                    $run = 'delete';
                    break;
            }
            //si on a une action on la supprime et on arrête la boucle pour ne pas avoir plusierus deck créer
            if ($run != '') {
                unset($json[$action][$titre]);
                file_put_contents('../deck.json', json_encode($json, JSON_PRETTY_PRINT));
                break;
            }
        } else {
            $error = [];
            if (strtolower(date('l')) != $value['jour'])
                $error[] = 'jour';
            if (intval(date('H')) < intval($value['heure']))
                $error[] = 'heure';
            echo "temps pas bon pour $titre (" . implode(', ', $error) . ")\n";
        }
    }
}
