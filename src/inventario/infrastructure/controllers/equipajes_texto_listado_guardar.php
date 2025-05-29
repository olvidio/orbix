<?php

// grabar la cabecera, pie o texto en las maletas.
use src\inventario\application\repositories\EgmRepository;
use src\inventario\application\repositories\EquipajeRepository;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$error_txt = '';

$Qtexto = (string)filter_input(INPUT_POST, 'texto');
$Qloc = (string)filter_input(INPUT_POST, 'loc');
$Qid_equipaje = (int)filter_input(INPUT_POST, 'id_equipaje');

$EquipajeRepository = new EquipajeRepository();
switch ($Qloc) {
    case 'cabecera':
        $oEquipaje = $EquipajeRepository->findById($Qid_equipaje);
        $oEquipaje->setCabecera($Qtexto);
        if ($EquipajeRepository->Guardar($oEquipaje) === false) {
            $error_txt .= _("hay un error, no se ha guardado");
            $error_txt .= "\n" . $EquipajeRepository->getErrorTxt();
        }
        break;
    case 'cabeceraB':
        $oEquipaje = $EquipajeRepository->findById($Qid_equipaje);
        $oEquipaje->setCabeceraB($Qtexto);
        if ($EquipajeRepository->Guardar($oEquipaje) === false) {
            $error_txt .= _("hay un error, no se ha guardado");
            $error_txt .= "\n" . $EquipajeRepository->getErrorTxt();
        }
        break;
    case 'firma':
        $oEquipaje = $EquipajeRepository->findById($Qid_equipaje);
        $oEquipaje->setFirma($Qtexto);
        if ($EquipajeRepository->Guardar($oEquipaje) === false) {
            $error_txt .= _("hay un error, no se ha guardado");
            $error_txt .= "\n" . $EquipajeRepository->getErrorTxt();
        }
        break;
    case 'pie':
        $oEquipaje = $EquipajeRepository->findById($Qid_equipaje);
        $oEquipaje->setPie($Qtexto);
        if ($EquipajeRepository->Guardar($oEquipaje) === false) {
            $error_txt .= _("hay un error, no se ha guardado");
            $error_txt .= "\n" . $EquipajeRepository->getErrorTxt();
        }
        break;
    default:
        preg_match('/docs_grupo_(.*)/', $Qloc, $matches);
        $id_grupo = $matches[1];

        $EgmRepository = new EgmRepository();
        $aWhere = ['id_equipaje' => $Qid_equipaje, 'id_grupo' => $id_grupo];
        $cEgm = $EgmRepository->getEgmes($aWhere);
        $oEgm = $cEgm[0];

        $oEgm->setTexto($Qtexto);
        if ($EgmRepository->Guardar($oEgm) === false) {
            $error_txt .= _("hay un error, no se ha guardado");
            $error_txt .= "\n" . $EgmRepository->getErrorTxt();
        }
}

// envía una Response
$jsondata = ContestarJson::respuestaPhp($error_txt, 'ok');
ContestarJson::send($jsondata);
