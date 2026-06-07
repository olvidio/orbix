<?php

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use src\shared\infrastructure\DependencyResolver;

// grabar la cabecera, pie o texto en las maletas.
use src\inventario\domain\contracts\EgmRepositoryInterface;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\shared\web\ContestarJson;

$error_txt = '';

$Qtexto = input_string($_POST, 'texto');
$Qloc = input_string($_POST, 'loc');
$Qid_equipaje = input_int($_POST, 'id_equipaje');

/** @var EquipajeRepositoryInterface $EquipajeRepository */
$EquipajeRepository = DependencyResolver::get(EquipajeRepositoryInterface::class);
switch ($Qloc) {
    case 'cabecera':
        $oEquipaje = $EquipajeRepository->findById($Qid_equipaje);
        if ($oEquipaje === null) {
            break;
        }
        $oEquipaje->setCabecera($Qtexto);
        if ($EquipajeRepository->Guardar($oEquipaje) === false) {
            $error_txt .= _("hay un error, no se ha guardado");
            $error_txt .= "\n" . $EquipajeRepository->getErrorTxt();
        }
        break;
    case 'cabeceraB':
        $oEquipaje = $EquipajeRepository->findById($Qid_equipaje);
        if ($oEquipaje === null) {
            break;
        }
        $oEquipaje->setCabeceraB($Qtexto);
        if ($EquipajeRepository->Guardar($oEquipaje) === false) {
            $error_txt .= _("hay un error, no se ha guardado");
            $error_txt .= "\n" . $EquipajeRepository->getErrorTxt();
        }
        break;
    case 'pie':
        $oEquipaje = $EquipajeRepository->findById($Qid_equipaje);
        if ($oEquipaje === null) {
            break;
        }
        $oEquipaje->setPie($Qtexto);
        if ($EquipajeRepository->Guardar($oEquipaje) === false) {
            $error_txt .= _("hay un error, no se ha guardado");
            $error_txt .= "\n" . $EquipajeRepository->getErrorTxt();
        }
        break;
    default:
        preg_match('/docs_grupo_(.*)/', $Qloc, $matches);
        $id_grupo = $matches[1] ?? '';
        if ($id_grupo === '') {
            break;
        }

        /** @var EgmRepositoryInterface $EgmRepository */
$EgmRepository = DependencyResolver::get(EgmRepositoryInterface::class);
        $aWhere = ['id_equipaje' => $Qid_equipaje, 'id_grupo' => $id_grupo];
        $cEgm = $EgmRepository->getEgmes($aWhere);
        $oEgm = $cEgm[0];

        $oEgm->setTextoVo($Qtexto);
        if ($EgmRepository->Guardar($oEgm) === false) {
            $error_txt .= _("hay un error, no se ha guardado");
            $error_txt .= "\n" . $EgmRepository->getErrorTxt();
        }
}

// envía una Response
ContestarJson::enviar($error_txt, 'ok');
