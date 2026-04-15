<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Desplegable;
use web\Hash;

require_once("frontend/shared/global_header_front.inc");

$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qmod = (string)filter_input(INPUT_POST, 'mod');
$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');
$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$s_pkey = (string)filter_input(INPUT_POST, 's_pkey');

$data = PostRequest::getDataFromUrl('/src/ubis/teleco_editar', [
    'obj_pau' => $Qobj_pau,
    'mod' => $Qmod,
    'id_ubi' => $Qid_ubi,
    'sel' => $a_sel,
    's_pkey' => $s_pkey,
]);

$oDesplegableTiposTeleco = new Desplegable();
$oDesplegableTiposTeleco->setNombre('id_tipo_teleco');
$oDesplegableTiposTeleco->setOpciones($data['a_tipos']);
$oDesplegableTiposTeleco->setOpcion_sel($data['id_tipo_teleco']);
$oDesplegableTiposTeleco->setAction('fnjs_actualizar_descripcion()');
$oDesplegableTiposTeleco->setBlanco(true);

$oDesplegableDescTeleco = new Desplegable();
$oDesplegableDescTeleco->setOpciones($data['a_desc']);
$oDesplegableDescTeleco->setNombre('id_desc_teleco');
$oDesplegableDescTeleco->setOpcion_sel($data['id_desc_teleco']);
$oDesplegableDescTeleco->setBlanco(true);

$oHash = new Hash();
$oHash->setCamposForm('mod!id_tipo_teleco!id_desc_teleco!num_teleco!observ');
$oHash->setcamposNo('mod!');
$oHash->setArraycamposHidden([
    'campos_chk' => '',
    'obj_pau' => $Qobj_pau,
    'id_ubi' => $Qid_ubi,
    's_pkey' => $s_pkey,
]);

$a_campos = [
    'obj' => $data['obj'],
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oDesplegableTiposTeleco' => $oDesplegableTiposTeleco,
    'oDesplegableDescTeleco' => $oDesplegableDescTeleco,
    'num_teleco' => $data['num_teleco'],
    'observ' => $data['observ'],
    'botones' => $data['botones'],
    'url_actualizar' => 'frontend/ubis/controller/teleco_desc_lista_ajax.php',
    'url_guardar' => 'frontend/ubis/controller/teleco_guardar_ajax.php',
];

$oView = new ViewNewPhtml('frontend\ubis\controller');
$oView->renderizar('teleco_form.phtml', $a_campos);
