<?php

use src\shared\domain\DatosUpdateRepo;
use src\shared\infrastructure\DatosInfoRepoResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

$Qclase_info_encoded = (string)\src\shared\domain\helpers\FilterPostGet::post('clase_info');
$Qs_pkey = (string)\src\shared\domain\helpers\FilterPostGet::post('s_pkey');
$Qid_pau = (string)\src\shared\domain\helpers\FilterPostGet::post('id_pau');
$Qmod = (string)\src\shared\domain\helpers\FilterPostGet::post('mod');
$Qobj_pau = (string)\src\shared\domain\helpers\FilterPostGet::post('obj_pau');
$Qgo_to = (string)\src\shared\domain\helpers\FilterPostGet::post('go_to');

// Cuando es eliminar, viene directamente de la tabla (mod_tabla_sql)
// Como es borrar, no hace falta mantener el scroll
$a_sel = (array)\src\shared\domain\helpers\FilterPostGet::post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$stack = '';
if (!empty($a_sel)) { //vengo de un checkbox
    $sel0 = $a_sel[0] ?? '';
    $Qs_pkey = explode('#', (string) $sel0);
    // he cambiado las comillas dobles por simples. Deshago el cambio.
    $Qs_pkey = str_replace("'", '"', $Qs_pkey[0]);
}

$pkeyJson = src\shared\domain\helpers\FuncTablasSupport::urlsafeB64decode($Qs_pkey);
$a_pkey = $pkeyJson !== '' ? json_decode($pkeyJson, true) : null;

// Tiene que ser en dos pasos.
$obj = urldecode($Qclase_info_encoded);
$oInfoClase = DatosInfoRepoResolver::resolve($obj);
$oInfoClase->setMod($Qmod);
$oInfoClase->setA_pkey($a_pkey); //Para eliminar y editar
$oInfoClase->setId_pau($Qid_pau); //Para nuevo
if (method_exists($oInfoClase, 'setObj_pau')) {
    $oInfoClase->setObj_pau($Qobj_pau);
}
$oFicha = $oInfoClase->getFicha();

$repositoryInterface = $oInfoClase->getRepositoryInterface();

$oDatosUpdate = new DatosUpdateRepo();
$oDatosUpdate->setRepositoryInterface($repositoryInterface);
$oDatosUpdate->setFicha($oFicha);

// campos del dossier (de hecho todo el $_POST, porque desconozco...)
$oDatosUpdate->setCampos($_POST);

$rta = _("no se ha ejecutado la acción");
switch ($Qmod) {
    case 'eliminar':
        $rta = $oDatosUpdate->eliminar();
        break;
    case 'editar':
        $rta = $oDatosUpdate->editar();
        break;
    case 'nuevo':
        $rta = $oDatosUpdate->nuevo();
        break;
}

$error_txt = '';
if ($rta !== true) {
    $error_txt = (string)$rta;
}
ContestarJson::enviar($error_txt, 'ok');
