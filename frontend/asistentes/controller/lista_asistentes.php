<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/asistentes_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';

$oPosicion = FrontBootstrap::boot();
$Qrefresh = (int) filter_input(INPUT_POST, 'refresh');

$stackFromPost = list_nav_stack_from_post();
if ($stackFromPost !== 0 && $oPosicion->goStack($stackFromPost)) {
    $oPosicion->olvidar($stackFromPost);
}

if ($stackFromPost !== 0) {
    list_nav_boot_list_page_after_stack_return($oPosicion, $stackFromPost);
} else {
    list_nav_boot_actividad_select_child_recordar($oPosicion, $Qrefresh);
}
list_nav_persist_actividad_select_child_entry($oPosicion);


$campos = array_merge($_GET, $_POST);
$payload = asistentes_post_data(PostRequest::getDataFromUrl('/src/asistentes/lista_asistentes_data', $campos));

$a_campos = array_merge($payload, ['oPosicion' => $oPosicion]);

(new ViewNewPhtml('frontend\\asistentes\\controller'))
    ->renderizar('lista_asistentes.phtml', $a_campos);
