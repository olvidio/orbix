<?php

use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\configuracion\helpers\ModulosFormRender;
use frontend\shared\FrontBootstrap;
use frontend\configuracion\helpers\ConfiguracionPayload;
use frontend\shared\helpers\ListNavSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
require_once 'frontend/shared/web/func_web.php';

$Qrefresh = (int)filter_input(INPUT_POST, 'refresh');

$campos = array_merge($_GET, $_POST);

$Qmod = \frontend\shared\helpers\PayloadCoercion::string($campos['mod'] ?? '');
$stackFromPost = isset($campos['stack']) ? (string) filter_var($campos['stack'], FILTER_SANITIZE_NUMBER_INT) : '';
if ($Qmod !== 'nuevo' && $stackFromPost !== '' && $oPosicion->goStack($stackFromPost)) {
    $oPosicion->olvidar($stackFromPost);
}

\frontend\shared\helpers\ListNavSupport::bootRecordar($oPosicion, $Qrefresh);
\frontend\shared\helpers\ListNavSupport::persistRecordarEntry($oPosicion, \frontend\shared\helpers\ListNavSupport::mergeSelectionIntoReturnParametros(\frontend\shared\helpers\ListNavSupport::buildReturnParametrosFromPost(), \frontend\shared\helpers\ListNavSupport::idSelFromPost(), \frontend\shared\helpers\ListNavSupport::scrollIdFromPost()));


$data = PostRequest::getDataFromUrl('/src/configuracion/modulos_form_data', $campos);
$payload = ConfiguracionPayload::stringKeyPayload($data);
$payload = ModulosFormRender::enrich($payload);
$view = ConfiguracionPayload::modulosFormViewFromPayload($payload);

$a_campos = [
    'oPosicion' => $oPosicion,
    'hash_form_html' => $view['hash_form_html'],
    'hash_actualizar_html' => $view['hash_actualizar_html'],
    'id_mod' => $view['id_mod'],
    'nom' => $view['nom'],
    'descripcion' => $view['descripcion'],
    'a_mods_todos' => $view['a_mods_todos'],
    'a_apps_todas' => $view['a_apps_todas'],
    'a_mods_req' => $view['a_mods_req'],
    'a_apps_req' => $view['a_apps_req'],
    'a_apps_mod' => $view['a_apps_mod'],
];

$oView = new ViewNewPhtml('frontend\\configuracion\\view');
$oView->renderizar('modulos_form.phtml', $a_campos);
