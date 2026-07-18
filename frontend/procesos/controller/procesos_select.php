<?php

use frontend\notas\helpers\NotasFormSupport;
use frontend\shared\config\AppUrlConfig;
use frontend\procesos\support\ProcesosHashes;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\web\Desplegable;
use frontend\shared\web\Posicion;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');
$restored = ListNavSupport::restoreSelectionFromStackPost();

/** @var string|list<string> $Qid_sel */
$Qid_sel = !ListNavSupport::idSelIsEmpty($restored['id_sel']) ? $restored['id_sel'] : ListNavSupport::idSelFromPost();
$Qscroll_id = $restored['scroll_id'] !== '' ? $restored['scroll_id'] : ListNavSupport::scrollIdFromPost();
$navState = ListNavSupport::mergeSelectionIntoReturnParametros(
    ListNavSupport::buildReturnParametrosFromPost(),
    $Qid_sel,
    $Qscroll_id,
);
$oPosicion->nav()->enter(
    (string) ($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    [],
    $navState,
);
ListNavSupport::syncNavStateAt(
    $oPosicion,
    1,
    ListNavSupport::buildSelectionStatePatchFromPost(),
);


// Si vengo por medio de Posicion, borro la última

$data = PostRequest::getDataFromUrl('/src/procesos/procesos_select_data', []);
$aTiposProceso = NotasFormSupport::desplegableOpciones($data['a_tipos_proceso'] ?? []);

$oDespl = new Desplegable();
$oDespl->setOpciones($aTiposProceso);
$oDespl->setBlanco(true);

// Endpoints por accion (slice 10: split de procesos_ajax). url_ver apunta
// al frontend controller migrado en el slice 2.
$apiBase = AppUrlConfig::getApiBaseUrl();
$url_regenerar = AppUrlConfig::srcBrowserUrl('/src/procesos/procesos_regenerar');
$url_clonar = AppUrlConfig::srcBrowserUrl('/src/procesos/procesos_clonar');
// url_get / url_get_listado apuntan al renderer frontend que consume
// los endpoints /src/procesos/procesos_get(_listado) y devuelve HTML.
$url_get = 'frontend/procesos/controller/procesos_get.php';
$url_get_listado = 'frontend/procesos/controller/procesos_get_listado.php';
$url_update = AppUrlConfig::srcBrowserUrl('/src/procesos/procesos_update');
$url_eliminar = AppUrlConfig::srcBrowserUrl('/src/procesos/procesos_eliminar');
$url_ver = 'frontend/procesos/controller/procesos_ver.php';

$h_regenerar = ProcesosHashes::formLink($url_regenerar, 'id_tipo_proceso');
$h_get = ProcesosHashes::formLink($url_get, 'id_tipo_proceso');
$h_get_listado = ProcesosHashes::formLink($url_get_listado, 'id_tipo_proceso');
$h_clonar = ProcesosHashes::formLink($url_clonar, 'id_tipo_proceso!id_tipo_proceso_ref');
$h_eliminar = ProcesosHashes::formLink($url_eliminar, 'id_item');
$h_nuevo = ProcesosHashes::formLink($url_ver, 'mod!id_tipo_proceso');
$h_modificar = ProcesosHashes::formLink($url_ver, 'mod!id_item!id_tipo_proceso');

$txt_eliminar = _("¿Esta seguro que desea borrar esta fase?");
$txt_clonar = _("No ha determinado para que proceso");

$a_campos = [
    'oPosicion' => $oPosicion,
    'h_regenerar' => $h_regenerar,
    'h_get' => $h_get,
    'h_get_listado' => $h_get_listado,
    'h_clonar' => $h_clonar,
    'h_eliminar' => $h_eliminar,
    'h_nuevo' => $h_nuevo,
    'h_modificar' => $h_modificar,
    'oDespl' => $oDespl,
    'url_regenerar' => $url_regenerar,
    'url_clonar' => $url_clonar,
    'url_get' => $url_get,
    'url_get_listado' => $url_get_listado,
    'url_update' => $url_update,
    'url_eliminar' => $url_eliminar,
    'url_ver' => $url_ver,
    'txt_eliminar' => $txt_eliminar,
    'txt_clonar' => $txt_clonar,
];

$oView = new ViewNewTwig('frontend/procesos/controller');
$oView->renderizar('procesos_select.html.twig', $a_campos);
