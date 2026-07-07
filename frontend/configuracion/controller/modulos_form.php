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
$Qid_mod = \frontend\shared\helpers\PayloadCoercion::string($campos['id_mod'] ?? '');
if ($Qmod !== 'nuevo') {
    ListNavSupport::restoreSelectionFromStackPost();
}

$navIdentity = $Qmod !== 'nuevo' && $Qid_mod !== '' ? ['id_mod' => $Qid_mod] : [];
$navState = ListNavSupport::mergeSelectionIntoReturnParametros(
    ListNavSupport::buildReturnParametrosFromPost(),
    ListNavSupport::idSelFromPost(),
    ListNavSupport::scrollIdFromPost(),
);
$oPosicion->nav()->enter(
    (string) ($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    $navIdentity,
    $navState,
);
ListNavSupport::syncNavStateAt(
    $oPosicion,
    1,
    ListNavSupport::buildSelectionStatePatchFromPost(),
);


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
