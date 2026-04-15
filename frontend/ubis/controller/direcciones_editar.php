<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Hash;
use function core\is_true;

require_once("frontend/shared/global_header_front.inc");

$Qrefresh = (int)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');
$Qmod = (string)filter_input(INPUT_POST, 'mod');
$Qobj_dir = (string)filter_input(INPUT_POST, 'obj_dir');
$Qid_direccion = (string)filter_input(INPUT_POST, 'id_direccion');
$Qidx = (int)filter_input(INPUT_POST, 'idx');
$Qinc = (string)filter_input(INPUT_POST, 'inc');

$data = PostRequest::getDataFromUrl('/src/ubis/direcciones_editar', [
    'id_ubi' => $Qid_ubi,
    'mod' => $Qmod,
    'obj_dir' => $Qobj_dir,
    'id_direccion' => $Qid_direccion,
    'idx' => $Qidx,
    'inc' => $Qinc,
]);

if (!empty($data['sin_direccion'])) {
    echo "<table><tr><td>{$data['msg_sin_direccion']}</td></tr></table><br>";
    $golistadir = Hash::link('frontend/ubis/controller/direcciones_que.php?' . http_build_query(['id_ubi' => $Qid_ubi, 'obj_dir' => $Qobj_dir]));
    echo "<span class='link' onclick=\"fnjs_update_div('#ficha','$golistadir');\">" . mb_strtoupper(_("asignar una dirección")) . "</span>";
    return;
}

$oHash = new Hash();
$campos_chk = 'cp_dcha!propietario!principal';
$oHash->setCamposForm('a_p!c_p!direccion!f_direccion!latitud!longitud!nom_sede!observ!pais!poblacion!provincia!que');
$oHash->setcamposNo('que!inc');
$oHash->setCamposChk($campos_chk);
$oHash->setArraycamposHidden([
    'campos_chk' => $campos_chk,
    'obj_dir' => $Qobj_dir,
    'id_direccion' => $Qid_direccion,
    'idx' => $data['idx'],
    'id_ubi' => $Qid_ubi,
]);

$goInfo = Hash::link(ConfigGlobal::getWeb() . '/frontend/ubis/controller/info_ubis.php?' . http_build_query(['id_item' => 1]));
$golistadir = Hash::link('frontend/ubis/controller/direcciones_que.php?' . http_build_query(['id_ubi' => $Qid_ubi, 'obj_dir' => $Qobj_dir]));
$go_dir = Hash::link('frontend/ubis/controller/direcciones_editar.php?' . http_build_query([
    'id_ubi' => $Qid_ubi,
    'id_direccion' => $Qid_direccion,
    'obj_dir' => $Qobj_dir,
    'idx' => $data['idx'],
    'refresh' => 1,
]));

$oHashPlano = new Hash();
$oHashPlano->setUrl('frontend/ubis/controller/direcciones_asignar.php');
$oHashPlano->setCamposForm('obj_dir!id_ubi!id_direccion');
$h_asignar = $oHashPlano->linkSinVal();

$oHashPlano2 = new Hash();
$oHashPlano2->setUrl('frontend/ubis/controller/plano_bytea.php');
$oHashPlano2->setCamposForm('obj_dir!act!id_direccion');
$h = $oHashPlano2->linkSinVal();

$a_campos = array_merge($data, [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'id_ubi' => $Qid_ubi,
    'obj_dir' => $Qobj_dir,
    'chk_propietario' => is_true($data['propietario']) ? 'checked' : '',
    'chk_principal' => is_true($data['principal']) ? 'checked' : '',
    'chk_dcha' => is_true($data['cp_dcha']) ? 'checked' : '',
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
