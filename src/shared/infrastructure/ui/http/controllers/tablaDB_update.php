<?php

use src\shared\domain\DatosUpdateRepo;
use src\shared\infrastructure\DatosInfoRepoResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

$ctxEliminar = (string)FilterPostGet::post('ctx_eliminar');
$selIn = (array)FilterPostGet::post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if ($ctxEliminar === '' && $selIn !== []) {
    $first = $selIn[0] ?? '';
    $ctxEliminar = is_scalar($first) ? (string) $first : '';
}

try {
    if ($ctxEliminar !== '') {
        $ctx = HashB::open($ctxEliminar, 'tablaDB_update');
        $Qmod = 'eliminar';
    } else {
        $ctx = HashB::open((string)FilterPostGet::post('ctx_update'), 'tablaDB_update');
        $Qmod = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'mod');
    }
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$Qclase_info_encoded = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'clase_info');
$Qs_pkey = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 's_pkey');
$Qid_pau = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'id_pau');
$Qobj_pau = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'obj_pau');
$Qgo_to = (string)FilterPostGet::post('go_to');

$pkeyJson = src\shared\domain\helpers\FuncTablasSupport::urlsafeB64decode($Qs_pkey);
$a_pkey = $pkeyJson !== '' ? json_decode($pkeyJson, true) : null;

$obj = urldecode($Qclase_info_encoded);
$oInfoClase = DatosInfoRepoResolver::resolve($obj);
$oInfoClase->setMod($Qmod);
$oInfoClase->setA_pkey($a_pkey);
$oInfoClase->setId_pau($Qid_pau);
if (method_exists($oInfoClase, 'setObj_pau')) {
    $oInfoClase->setObj_pau($Qobj_pau);
}
$oFicha = $oInfoClase->getFicha();

$repositoryInterface = $oInfoClase->getRepositoryInterface();

$oDatosUpdate = new DatosUpdateRepo();
$oDatosUpdate->setRepositoryInterface($repositoryInterface);
$oDatosUpdate->setFicha($oFicha);
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
