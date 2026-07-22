<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\configuracion\helpers\ModulosSelectRender;
use frontend\shared\FrontBootstrap;
use frontend\configuracion\helpers\ConfiguracionPayload;
use frontend\shared\helpers\ListNavSupport;
use frontend\shared\helpers\PayloadCoercion;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$aGoBack = ['mod' => ''];

$campos = array_merge($_GET, $_POST);

$restored = ListNavSupport::restoreSelectionFromStackPost();
/** @var string|list<string> $Qid_sel */
$Qid_sel = !ListNavSupport::idSelIsEmpty($restored['id_sel']) ? $restored['id_sel'] : ListNavSupport::idSelFromPost();
$Qscroll_id = $restored['scroll_id'] !== '' ? $restored['scroll_id'] : ListNavSupport::scrollIdFromPost();
if (!ListNavSupport::idSelIsEmpty($restored['id_sel'])) {
    $campos['restored_id_sel'] = $restored['id_sel'];
    $campos['restored_scroll_id'] = $restored['scroll_id'];
}

$navState = ListNavSupport::mergeSelectionForRecordar($aGoBack, $Qid_sel, $Qscroll_id);
$oPosicion->nav()->enter(
    PayloadCoercion::string($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    [],
    $navState,
);
ListNavSupport::syncNavStateAt(
    $oPosicion,
    1,
    array_merge($aGoBack, ListNavSupport::buildSelectionStatePatchFromPost()),
);

$data = PostRequest::getDataFromUrl('/src/configuracion/modulos_select_data', $campos);
$payload = ConfiguracionPayload::stringKeyPayload($data);
$payload = ModulosSelectRender::enrich($payload);
$view = ConfiguracionPayload::modulosSelectViewFromPayload($payload);

$oTabla = new Lista();
$oTabla->setId_tabla('modulos_select');
$oTabla->setCabeceras($view['a_cabeceras']);
$oTabla->setBotones($view['a_botones']);
$oTabla->setDatos($view['a_valores']);

$a_campos = [
    'oPosicion' => $oPosicion,
    'hash_lista_html' => $view['hash_lista_html'],
    'oTabla' => $oTabla,
    'txt_eliminar' => $view['txt_eliminar'],
    'txt_anadir_modulo' => $view['txt_anadir_modulo'],
];

$oView = new ViewNewPhtml('frontend\\configuracion\\view');
$oView->renderizar('modulos_select.phtml', $a_campos);
