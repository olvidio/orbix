<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_activ = (integer)filter_input(INPUT_GET, 'id_activ');

$url_backend = '/src/ubiscamas/actividad_habitaciones_lista';
$a_campos_backend = ['id_activ' => $Qid_activ];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);

if (isset($data['error'])) {
    exit($data['error']);
}

$a_campos = [
    'id_activ' => $data['id_activ'],
    'habitaciones_con_camas' => $data['habitaciones_con_camas'],
    'camas_con_asistentes' => $data['camas_con_asistentes'],
];

$oView = new ViewNewPhtml('frontend\\ubiscamas\\controller');
$oView->renderizar('lista_habitaciones_distribucion.phtml', $a_campos);
