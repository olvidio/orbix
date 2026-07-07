<?php

use frontend\ubis\helpers\UbisPayload;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Posicion;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;
use frontend\shared\helpers\FuncTablasSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
/** @var Posicion $oPosicion */

$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');
$Qmod = (string)filter_input(INPUT_POST, 'mod');
$Qobj_dir = (string)filter_input(INPUT_POST, 'obj_dir');
$Qid_direccion = (string)filter_input(INPUT_POST, 'id_direccion');
$Qidx = (int)filter_input(INPUT_POST, 'idx');
$Qinc = (string)filter_input(INPUT_POST, 'inc');

$navIdentity = ['id_ubi' => $Qid_ubi];
if ($Qid_direccion !== '') {
    $navIdentity['id_direccion'] = $Qid_direccion;
}
$navState = ListNavSupport::mergeSelectionForRecordar(
    ListNavSupport::buildReturnParametrosFromPost(),
    ListNavSupport::idSelFromPost(),
    ListNavSupport::scrollIdFromPost(),
);
$oPosicion->nav()->enter(
    (string) ($_SERVER['PHP_SELF'] ?? ''),
    '#ficha',
    $navIdentity,
    $navState,
);
ListNavSupport::syncNavStateAt(
    $oPosicion,
    1,
    ListNavSupport::mergeSelectionForRecordar(
        ['id_ubi' => $Qid_ubi, 'obj_dir' => $Qobj_dir],
        ListNavSupport::idSelFromPost(),
        ListNavSupport::scrollIdFromPost(),
    ),
);

$data = UbisPayload::postData(PostRequest::getDataFromUrl('/src/ubis/direcciones_editar', [
    'id_ubi' => $Qid_ubi,
    'mod' => $Qmod,
    'obj_dir' => $Qobj_dir,
    'id_direccion' => $Qid_direccion,
    'idx' => $Qidx,
    'inc' => $Qinc,
]));

if (!empty($data['sin_direccion'])) {
    echo '<table><tr><td>' . \frontend\shared\helpers\PayloadCoercion::string($data['msg_sin_direccion'] ?? '') . '</td></tr></table><br>';
    $golistadir = HashFront::link('frontend/ubis/controller/direcciones_que.php?' . http_build_query(['id_ubi' => $Qid_ubi, 'obj_dir' => $Qobj_dir]));
    echo "<span class='link' onclick=\"fnjs_update_div('#ficha','$golistadir');\">" . mb_strtoupper(_("asignar una dirección")) . "</span>";
    return;
}

$oHash = new HashFront();
$campos_chk = 'cp_dcha!propietario!principal';
$oHash->setCamposForm('a_p!c_p!direccion!f_direccion!latitud!longitud!nom_sede!observ!pais!poblacion!provincia!que');
$oHash->setcamposNo('que!inc');
$oHash->setCamposChk($campos_chk);
$oHash->setArraycamposHidden([
    'campos_chk' => $campos_chk,
    'obj_dir' => $Qobj_dir,
    'id_direccion' => $Qid_direccion,
    'idx' => $data['idx'],
    'inc' => $Qinc,
    'id_ubi' => $Qid_ubi,
]);

$goInfo = HashFront::link(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/ubis/controller/info_ubis.php?' . http_build_query(['id_item' => 1]));
$golistadir = HashFront::link('frontend/ubis/controller/direcciones_que.php?' . http_build_query(['id_ubi' => $Qid_ubi, 'obj_dir' => $Qobj_dir]));

$oHashGo = new HashFront();
$oHashGo->setUrl('frontend/ubis/controller/direcciones_editar.php?');
$oHashGo->setcamposNo('inc');
$oHashGo->setArrayCamposHidden([
    'id_ubi' => $Qid_ubi,
    'id_direccion' => $Qid_direccion,
    'obj_dir' => $Qobj_dir,
    'idx' => $data['idx'],
    'inc' => $Qinc,
    'refresh' => 1,
]);
$h = $oHashGo->linkConVal();
$go_dir = 'frontend/ubis/controller/direcciones_editar.php?'.$h;

$oHashPlano = new HashFront();
$oHashPlano->setUrl('frontend/ubis/controller/direcciones_asignar.php');
$oHashPlano->setCamposForm('obj_dir!id_ubi!id_direccion');
$h_asignar = $oHashPlano->linkSinValParams();

$oHashPlano2 = new HashFront();
$oHashPlano2->setUrl('frontend/ubis/controller/plano_bytea.php');
$oHashPlano2->setCamposForm('obj_dir!act!id_direccion');
$h = $oHashPlano2->linkSinValParams();

$a_campos = UbisPayload::viewVars($data, [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'id_ubi' => $Qid_ubi,
    'obj_dir' => $Qobj_dir,
    'chk_propietario' => \src\shared\domain\helpers\FuncTablasSupport::isTrue($data['propietario']) ? 'checked' : '',
    'chk_principal' => \src\shared\domain\helpers\FuncTablasSupport::isTrue($data['principal']) ? 'checked' : '',
    'chk_dcha' => \src\shared\domain\helpers\FuncTablasSupport::isTrue($data['cp_dcha']) ? 'checked' : '',
    'golistadir' => $golistadir,
    'go_dir' => $go_dir,
    'h' => $h,
    'h_asignar' => $h_asignar,
    'goInfo' => $goInfo,
    'url_direccion_update' => 'frontend/ubis/controller/direccion_update.php',
    'url_direcciones_quitar' => 'frontend/ubis/controller/direcciones_quitar.php',
    'url_direcciones_asignar' => 'frontend/ubis/controller/direcciones_asignar.php',
]);

$oView = new ViewNewPhtml('frontend\ubis\controller');
$oView->renderizar('direccion_form.phtml', $a_campos);
