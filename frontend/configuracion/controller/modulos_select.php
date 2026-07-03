<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\configuracion\helpers\ModulosSelectRender;
use frontend\shared\FrontBootstrap;
use frontend\configuracion\helpers\ConfiguracionPayload;
use frontend\shared\helpers\ListNavSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$aGoBack = ['mod' => ''];

$campos = array_merge($_GET, $_POST);

$stackFromPost = isset($campos['stack']) ? (string) filter_var($campos['stack'], FILTER_SANITIZE_NUMBER_INT) : '';
if ($stackFromPost !== '' && $oPosicion->goStack($stackFromPost)) {
    $campos['restored_id_sel']    = $oPosicion->getParametro('id_sel');
    $campos['restored_scroll_id'] = $oPosicion->getParametro('scroll_id');
    $oPosicion->olvidar($stackFromPost);
}

\frontend\shared\helpers\ListNavSupport::bootRecordar($oPosicion);
\frontend\shared\helpers\ListNavSupport::persistRecordarEntry($oPosicion, \frontend\shared\helpers\ListNavSupport::mergeSelectionForRecordar($aGoBack, \frontend\shared\helpers\ListNavSupport::idSelFromPost(), \frontend\shared\helpers\ListNavSupport::scrollIdFromPost()));


$oPosicion->setParametros($aGoBack, 1);

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
