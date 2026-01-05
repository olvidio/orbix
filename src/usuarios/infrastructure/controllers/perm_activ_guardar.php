<?php

use procesos\model\PermAfectados;
use src\procesos\domain\contracts\PermUsuarioActividadRepositoryInterface;
use src\procesos\domain\entity\PermUsuarioActividad;
use web\ContestarJson;
use function core\is_true;

// FIN de  Cabecera global de URL de controlador **********

$error_txt = '';

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
$Qid_tipo_activ = (integer)filter_input(INPUT_POST, 'id_tipo_activ');
$Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
$Qdl_propia = (string)filter_input(INPUT_POST, 'dl_propia');
$QaFase_ref = (array)filter_input(INPUT_POST, 'fase_ref', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$QaPerm_on = (array)filter_input(INPUT_POST, 'perm_on', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$QaPerm_off = (array)filter_input(INPUT_POST, 'perm_off', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$QaAfecta_a = (array)filter_input(INPUT_POST, 'afecta_a', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

if (empty($Qid_tipo_activ)) {
    $Qisfsv_val = (string)filter_input(INPUT_POST, 'isfsv_val');
    $Qiasistentes_val = (string)filter_input(INPUT_POST, 'iasistentes_val');
    $Qiactividad_val = (string)filter_input(INPUT_POST, 'iactividad_val');
    $Qinom_tipo_val = (string)filter_input(INPUT_POST, 'inom_tipo_val');

    $sfsv_val = empty($Qisfsv_val) ? '.' : $Qisfsv_val;
    $asistentes_val = empty($Qiasistentes_val) ? '.' : $Qiasistentes_val;
    $actividad_val = empty($Qiactividad_val) ? '.' : $Qiactividad_val;
    $nom_tipo_val = empty($Qinom_tipo_val) ? '...' : $Qinom_tipo_val;
    $id_tipo_activ_txt = $sfsv_val . $asistentes_val . $actividad_val . $nom_tipo_val;
} else {
    $id_tipo_activ_txt = $Qid_tipo_activ;
}

// afecta a:
$oCuadros = new PermAfectados();
$aAfecta_a = $oCuadros->getPermissions();
$PermUsuarioActividadRepository = $GLOBALS['container']->get(PermUsuarioActividadRepositoryInterface::class);
foreach ($aAfecta_a as $afecta_a) {
    $aWhere = [
        'id_usuario' => $Qid_usuario,
        'dl_propia' => $Qdl_propia,
        'id_tipo_activ_txt' => $id_tipo_activ_txt,
        'afecta_a' => $afecta_a,
    ];

    $fase_ref = '';
    $perm_on = '';
    $perm_off = '';
    // si tiene valor grabo, sino elimino:
    $eliminar = TRUE;
    if (in_array($afecta_a, $QaAfecta_a)) {
        $i = array_search($afecta_a, $QaAfecta_a);
        $fase_ref = $QaFase_ref[$i];
        // si no hay fase ref, hay que eliminar
        if (empty($fase_ref)) {
            $eliminar = TRUE;
        } else {
            $perm_off = empty($QaPerm_off[$i]) ? 0 : $QaPerm_off[$i];
            $perm_on = empty($QaPerm_on[$i]) ? 0 : $QaPerm_on[$i];
            $cPermUsuarioActividad = $PermUsuarioActividadRepository->getPermUsuarioActividades($aWhere);
            // Solamente debería haber uno???
            if (count($cPermUsuarioActividad) === 1) {
                $oPermUsuarioActividad = $cPermUsuarioActividad[0];
            } else {
                $newId_item = $PermUsuarioActividadRepository->getNewId();
                $oPermUsuarioActividad = new PermUsuarioActividad();
                $oPermUsuarioActividad->setId_item($newId_item);
            }
            $oPermUsuarioActividad->setId_usuario($Qid_usuario);
            $oPermUsuarioActividad->setId_tipo_activ_txt($id_tipo_activ_txt);
            $oPermUsuarioActividad->setDl_propia($Qdl_propia);
            $oPermUsuarioActividad->setAfecta_a($afecta_a);
            $oPermUsuarioActividad->setFase_ref($fase_ref);
            $oPermUsuarioActividad->setperm_on($perm_on);
            $oPermUsuarioActividad->setperm_off($perm_off);
            if ($PermUsuarioActividadRepository->Guardar($oPermUsuarioActividad) === false) {
                $error_txt .= _("hay un error, no se ha guardado");
                $error_txt .= "\n" . $PermUsuarioActividadRepository->getErrorTxt();
            }
            $eliminar = false;
        }
    }
    if (is_true($eliminar)) {
        $cPermUsuarioActividad = $PermUsuarioActividadRepository->getPermUsuarioActividades($aWhere);
        // Solamente debería haber uno???
        if (count($cPermUsuarioActividad) === 1) {
            $oPermUsuarioActividad = $cPermUsuarioActividad[0];
            if ($PermUsuarioActividadRepository->Eliminar($oPermUsuarioActividad) === false) {
                $error_txt .= _("hay un error.");
                $error_txt .= "\n" . $PermUsuarioActividadRepository->getErrorTxt();
            }
        }
    }
}

ContestarJson::enviar($error_txt, 'ok');