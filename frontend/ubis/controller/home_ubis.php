<?php

use frontend\ubis\helpers\UbisPayload;
use frontend\dossiers\helpers\DossiersListaRender;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\Posicion;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
$bloque = (string)filter_input(INPUT_POST, 'bloque');
if (!empty($bloque)) {
    $oPosicion->setBloque("#$bloque");
    $oPosicion->addParametro('bloque', $bloque);
}
$bloque = 'ficha';

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$Qid_sel = null;
$Qscroll_id = null;
if (isset($_POST['stack'])) {
    $stack = (int)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== 0) {
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack($stack)) {
            $obj_pau = $oPosicion2->getParametro('obj_pau');
            $id_ubi = $oPosicion2->getParametro('id_ubi');
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}
\frontend\shared\helpers\ListNavSupport::bootRecordar($oPosicion);
\frontend\shared\helpers\ListNavSupport::persistRecordarEntry($oPosicion, \frontend\shared\helpers\ListNavSupport::mergeSelectionForRecordar(\frontend\shared\helpers\ListNavSupport::buildReturnParametrosFromPost(), $Qid_sel, $Qscroll_id));


if (!empty($a_sel)) {
    $id_ubi = UbisPayload::idFromSelItem($a_sel[0] ?? '');
} else {
    $id_ubi = (int)filter_input(INPUT_POST, 'id_ubi');
}

$home = UbisPayload::homeFromPayload(UbisPayload::postData(PostRequest::getDataFromUrl('/src/ubis/home_ubis_data', ['id_ubi' => $id_ubi])));

$base = AppUrlConfig::getPublicAppBaseUrl();
$gohome = HashFront::link($base . '/frontend/ubis/controller/home_ubis.php?' . http_build_query(['id_ubi' => $id_ubi, 'obj_pau' => $home['obj_pau']]));
$godossiers = HashFront::link($base . '/frontend/dossiers/controller/dossiers_ver.php?' . http_build_query(['pau' => $home['pau'], 'id_pau' => $home['id_pau'], 'obj_pau' => $home['obj_pau']]));

$go_ubi = HashFront::link('frontend/ubis/controller/ubis_editar.php?' . http_build_query(['id_ubi' => $id_ubi, 'obj_pau' => $home['obj_pau'], 'bloque' => $bloque]));
$go_dir = HashFront::link('frontend/ubis/controller/direcciones_editar.php?' . http_build_query(['id_ubi' => $id_ubi, 'id_direccion' => $home['id_direccion'], 'obj_dir' => $home['obj_dir'], 'bloque' => $bloque]));
$go_tel = HashFront::link('frontend/ubis/controller/teleco_tabla.php?' . http_build_query(['id_ubi' => $id_ubi, 'obj_pau' => $home['obj_pau'], 'bloque' => $bloque]));

$alt = _("ver dossiers");
$dos = _("dossiers");
$txt = ucfirst(_("formato texto"));
$titulo = $home['nombre_ubi'];

$lista_dossiers_html = \frontend\dossiers\helpers\DossiersListaRender::render($home['pau'], $home['id_pau'], $home['obj_pau']);

$a_campos = ['oPosicion' => $oPosicion,
    'godossiers' => $godossiers,
    'alt' => $alt,
    'dos' => $dos,
    'gohome' => $gohome,
    'titulo' => $titulo,
    'dl' => $home['dl'],
    'region' => $home['region'],
    'direccion' => $home['direccion'],
    'c_p' => $home['c_p'],
    'poblacion' => $home['poblacion'],
    'telfs' => $home['telfs'],
    'fax' => $home['fax'],
    'mails' => $home['mails'],
    'go_ubi' => $go_ubi,
    'ubi' => $home['ubi'],
    'go_dir' => $go_dir,
    'go_tel' => $go_tel,
    'pau' => $home['pau'],
    'id_pau' => $home['id_pau'],
    'Qobj_pau' => $home['obj_pau'],
    'lista_dossiers_html' => $lista_dossiers_html,
];

$oView = new ViewNewPhtml('frontend\ubis\controller');
$oView->renderizar('home_ubis.phtml', $a_campos);
