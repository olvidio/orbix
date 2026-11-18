<?php

use src\shared\application\PermisoTablaGenerica;
use src\shared\domain\DatosFormRepo;
use src\shared\infrastructure\DependencyResolver;
use src\shared\infrastructure\DatosInfoRepoResolver;
use src\shared\security\HashB;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

$Qclase_info_encoded = \src\shared\domain\helpers\FilterPostGet::post('clase_info');
if ((string)$Qclase_info_encoded === '') {
    ContestarJson::enviar(sprintf(_("Faltan parámetros: %s"), 'clase_info'), 'none');
    return;
}
$a_pkey = \src\shared\domain\helpers\FilterPostGet::post('a_pkey');
if (is_string($a_pkey) && $a_pkey !== '') {
    $decoded = json_decode($a_pkey, true);
    // Aceptar PK escalar (p.ej. 5) y PK compuesta (objeto/array).
    if (json_last_error() === JSON_ERROR_NONE) {
        $a_pkey = $decoded;
    }
}
$Qobj_pau = \src\shared\domain\helpers\FilterPostGet::post('obj_pau');
$Qmod = \src\shared\domain\helpers\FilterPostGet::post('mod');
$mod = is_string($Qmod) ? $Qmod : '';

// Tiene que ser en dos pasos.
$obj = urldecode((string) $Qclase_info_encoded);
$oInfoClase = DatosInfoRepoResolver::resolve($obj);
if ($oInfoClase->isSoloLectura()) {
    // Tabla de solo consulta: no hay formulario de alta ni de modificación.
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}
// Solo alta o modificación, y solo quien puede modificar esta tabla (la regla con la que se muestra).
$idPauPermiso = (int) FilterPostGet::post('id_pau');
if (!in_array($mod, ['nuevo', 'editar'], true)
    || !DependencyResolver::get(PermisoTablaGenerica::class)->puedeModificar(
        (string) $Qclase_info_encoded,
        method_exists($oInfoClase, 'getId_dossier') ? (int) $oInfoClase->getId_dossier() : null,
        $idPauPermiso
    )) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}
if (method_exists($oInfoClase, 'setObj_pau')) {
    $oInfoClase->setObj_pau($Qobj_pau);
}
$oInfoClase->setMod($mod);
$oInfoClase->setA_pkey($a_pkey);
$oFicha = $oInfoClase->getFicha();
$aCamposDepende = $oInfoClase->getArrayCamposDepende();
$aOpciones_txt = [];
foreach ($aCamposDepende as $pKeyRepository => $campoDepende) {
    if ($mod === 'nuevo') {
       $id_pau = '';
       $valor_campo_depende = '';
    } elseif ($oFicha === null) {
        $id_pau = '';
        $valor_campo_depende = '';
    } else {
        $bbb = 'get' . ucfirst($pKeyRepository);
        $id_pau = $oFicha->$bbb();
        $aaa = 'get' . ucfirst($campoDepende);
        $valor_campo_depende = $oFicha->$aaa();
    }
    $opciones = $oInfoClase->getOpcionesParaCondicion($campoDepende, $id_pau, $valor_campo_depende);
    $aOpciones_txt[$campoDepende] = $opciones ?? '';
}

$oDatosFormRepo = new DatosFormRepo();
$oDatosFormRepo->setFicha($oFicha);
$oDatosFormRepo->setArrayOpcionesTxt($aOpciones_txt);
$oDatosFormRepo->setMod($mod);

$tit_txt = $oInfoClase->getTxtTitulo();
$explicacion_txt = $oInfoClase->getTxtExplicacion();

$camposForm = $oDatosFormRepo->getCamposForm();
$camposNo = $oDatosFormRepo->getCamposNo();

// Set the properties
if (!empty($oFicha)) {
    $oDatosFormRepo->setFicha($oFicha);
}

$oDatosFormRepo->setMod($mod);

// Get the form data
$formData = $oDatosFormRepo->getFormularioData();

$error_txt = '';
$data = $formData;
$data['explicacion_txt'] = $explicacion_txt;
$data['tit_txt'] = $tit_txt;
$Qid_pau = (string)FilterPostGet::post('id_pau');
// Se firma la clave del registro que se ha cargado (a_pkey), no la que llegue en el POST.
$sPkeyFirmada = ($mod === 'editar' && $a_pkey !== null && $a_pkey !== '')
    ? \src\shared\domain\helpers\FuncTablasSupport::urlsafeB64encode(json_encode($a_pkey, JSON_THROW_ON_ERROR))
    : '';
$data['ctx_update'] = HashB::sign('tablaDB_update', [
    'clase_info' => is_string($Qclase_info_encoded) ? $Qclase_info_encoded : '',
    'mod' => $mod,
    's_pkey' => $sPkeyFirmada,
    'id_pau' => $Qid_pau,
    'obj_pau' => is_string($Qobj_pau) ? $Qobj_pau : '',
]);

ContestarJson::enviar($error_txt, $data);