<?php

use core\ConfigGlobal;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaExRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroExRepositoryInterface;
use src\ubis\domain\entity\Direccion;
use web\DateTimeLocal;
use function core\is_true;

/**
 * Para asegurar que inicia la sesion, y poder acceder a los permisos
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oMiUsuario = ConfigGlobal::MiUsuario();

$Qque = (string)filter_input(INPUT_POST, 'que');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
$Qcampos_chk = (string)filter_input(INPUT_POST, 'campos_chk');

$campos_chk = empty($Qcampos_chk) ? [] : explode('!', $Qcampos_chk);

$Qidx = (string)filter_input(INPUT_POST, 'idx');
$Qobj_dir = (string)filter_input(INPUT_POST, 'obj_dir');
$Qpropietario = (string)filter_input(INPUT_POST, 'propietario');
$Qprincipal = (string)filter_input(INPUT_POST, 'principal');

$Qnom_sede = (string)filter_input(INPUT_POST, 'nom_sede');
$Qdireccion = (string)filter_input(INPUT_POST, 'direccion');
$Qa_p = (string)filter_input(INPUT_POST, 'a_p');
$Qc_p = (string)filter_input(INPUT_POST, 'c_p');
$Qpoblacion = (string)filter_input(INPUT_POST, 'poblacion');
$Qprovincia = (string)filter_input(INPUT_POST, 'provincia');
$Qpais = (string)filter_input(INPUT_POST, 'pais');
$Qobserv = (string)filter_input(INPUT_POST, 'observ');
$Qf_direccion = (string)filter_input(INPUT_POST, 'f_direccion');
$Qlatitud = (string)filter_input(INPUT_POST, 'latitud');
$Qlongitud = (string)filter_input(INPUT_POST, 'longitud');

$oF_direccion = new DateTimeLocal($Qf_direccion);

switch ($Qobj_dir) {
    case "DireccionCentroDl":
        $DireccionRepository = $GLOBALS['container']->get(DireccionCentroDlRepositoryInterface::class);
        $UbiRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        break;
    case "DireccionCentroEx":
        $DireccionRepository = $GLOBALS['container']->get(DireccionCentroExRepositoryInterface::class);
        $UbiRepository = $GLOBALS['container']->get(CentroExRepositoryInterface::class);
        break;
    case "DireccionCdcDl":
        $DireccionRepository = $GLOBALS['container']->get(DireccionCasaDlRepositoryInterface::class);
        $UbiRepository = $GLOBALS['container']->get(CasaDlRepositoryInterface::class);
        break;
    case "DireccionCdcEx":
        $DireccionRepository = $GLOBALS['container']->get(DireccionCasaExRepositoryInterface::class);
        $UbiRepository = $GLOBALS['container']->get(CasaExRepositoryInterface::class);
        break;
}


$oUbi = $UbiRepository->findById($Qid_ubi);

if ($Qidx === 'nuevo') {
    $id_direccion = $DireccionRepository->getNewId();

    $oDireccion = new Direccion();
    $oDireccion->setId_direccion($id_direccion);

} else {
    // puede haber más de una dirección
    $a_id_direccion = explode(',', $_POST['id_direccion']);
    $id_direccion = $a_id_direccion[$Qidx];

    $oDireccion = (new $DireccionRepository())->findById($id_direccion);
}

$oDireccion->setNom_sede($Qnom_sede);
$oDireccion->setDireccion($Qdireccion);
$oDireccion->setA_p($Qa_p);
$oDireccion->setC_p($Qc_p);
$oDireccion->setPoblacion($Qpoblacion);
$oDireccion->setProvincia($Qprovincia);
$oDireccion->setPais($Qpais);
$oDireccion->setObserv($Qobserv);
$oDireccion->setF_direccion($oF_direccion);
$oDireccion->setLatitud((float)$Qlatitud);
$oDireccion->setLongitud((float)$Qlongitud);

$DireccionRepository->Guardar($oDireccion);

$oUbi->cambiarEstadoPropietario($id_direccion, is_true($Qpropietario));
if (is_true($Qprincipal)) {
    $oUbi->establecerDireccionPrincipal($id_direccion);
}

