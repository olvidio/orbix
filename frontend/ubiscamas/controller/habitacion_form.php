<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\ubiscamas\helpers\UbiscamasFormHashCompose;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/ubiscamas_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';

$oPosicion = FrontBootstrap::boot();
$Qrefresh = (int)filter_input(INPUT_POST, 'refresh');

$campos = array_merge($_GET, $_POST);

// Resolver estado de navegación aquí (frontend): recortar hacia delante desde $stack.
// Sólo tiene sentido si no se está creando una habitación nueva.
$Qnuevo = tessera_imprimir_string($campos['nuevo'] ?? '');
$stackFromPost = isset($campos['stack']) ? (string) filter_var($campos['stack'], FILTER_SANITIZE_NUMBER_INT) : '';
if ($Qnuevo === '' && $stackFromPost !== '' && $oPosicion->goStack($stackFromPost)) {
    $oPosicion->olvidar($stackFromPost);
}

list_nav_boot_recordar($oPosicion, $Qrefresh);
list_nav_persist_recordar_entry($oPosicion, list_nav_merge_selection_into_return_parametros(list_nav_build_return_parametros_from_post(), list_nav_id_sel_from_post(), list_nav_scroll_id_from_post()));


$data = ubiscamas_post_data(PostRequest::getDataFromUrl('/src/ubiscamas/habitacion_form_data', $campos));
$hashBlock = UbiscamasFormHashCompose::habitacionForm($data);

$a_campos = array_merge(
    ['oPosicion' => $oPosicion],
    ubiscamas_habitacion_form_view_from_payload($data, $hashBlock)
);

$oView = new ViewNewPhtml('frontend\\ubiscamas\\controller');
$oView->renderizar('habitacion_form.phtml', $a_campos);
