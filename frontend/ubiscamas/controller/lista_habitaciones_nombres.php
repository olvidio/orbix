<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\FrontBootstrap;

// Crea los objetos de uso global **********************************************
require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_activ = (integer)filter_input(INPUT_GET, 'id_activ');

$url_backend = '/src/ubiscamas/actividad_habitaciones_lista';
$a_campos_backend = ['id_activ' => $Qid_activ];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);

if (isset($data['error'])) {
    exit($data['error']);
}

// Flatten and sort by name
$a_lista = [];
foreach ($data['habitaciones_con_camas'] as $roomData) {
    $aHabitacion = $roomData['habitacion'];
    foreach ($roomData['camas'] as $aCama) {
        $id_cama = $aCama['id_cama'];
        $nombre_persona = '';
        if (isset($data['camas_con_asistentes'][$id_cama])) {
            $nombre_persona = $data['camas_con_asistentes'][$id_cama]['apellidos'];
        }
        
        // Only include if there's a person assigned (as per user image)
        if (!empty($nombre_persona)) {
            $a_lista[] = [
                'nombre' => $nombre_persona,
                'planta' => $aHabitacion['planta'],
                'habitacion' => $aHabitacion['nombre'],
            ];
        }
    }
}

// Sort by nombre (apellidos)
usort($a_lista, function($a, $b) {
    return strcasecmp($a['nombre'], $b['nombre']);
});

$a_campos = [
    'id_activ' => $Qid_activ,
    'a_lista' => $a_lista,
];

$oView = new ViewNewPhtml('frontend\\ubiscamas\\controller');
$oView->renderizar('lista_habitaciones_nombres.phtml', $a_campos);
