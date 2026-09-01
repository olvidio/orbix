<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\procesos\domain\contracts\PermUsuarioActividadRepositoryInterface;
use src\procesos\domain\entity\PermUsuarioActividad;
use src\procesos\domain\PermAfectadosBits;
use src\actividades\domain\value_objects\ActividadTipoIdTxt;
use src\shared\web\ContestarJson;
// FIN de  Cabecera global de URL de controlador **********

$error_txt = '';

$Qid_usuario = (integer)\src\shared\domain\helpers\FilterPostGet::post('id_usuario');
$Qid_tipo_activ = (string)(\src\shared\domain\helpers\FilterPostGet::post('id_tipo_activ') ?? '');
$Qid_item = (integer)\src\shared\domain\helpers\FilterPostGet::post('id_item');
$Qdl_propia = (string)\src\shared\domain\helpers\FilterPostGet::post('dl_propia');
$QaFase_ref = (array)\src\shared\domain\helpers\FilterPostGet::post('fase_ref', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$QaPerm_on = (array)\src\shared\domain\helpers\FilterPostGet::post('perm_on', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$QaPerm_off = (array)\src\shared\domain\helpers\FilterPostGet::post('perm_off', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$QaAfecta_a = (array)\src\shared\domain\helpers\FilterPostGet::post('afecta_a', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

if ($Qid_tipo_activ === '' || $Qid_tipo_activ === '0') {
    $Qisfsv_val = (string)\src\shared\domain\helpers\FilterPostGet::post('isfsv_val');
    $Qiasistentes_val = (string)\src\shared\domain\helpers\FilterPostGet::post('iasistentes_val');
    $Qiactividad_val = (string)\src\shared\domain\helpers\FilterPostGet::post('iactividad_val');
    $Qinom_tipo_val = (string)\src\shared\domain\helpers\FilterPostGet::post('inom_tipo_val');

    $id_tipo_activ_txt = ActividadTipoIdTxt::fromFormParts(
        $Qisfsv_val,
        $Qiasistentes_val,
        $Qiactividad_val,
        $Qinom_tipo_val,
    );
} else {
    $id_tipo_activ_txt = ActividadTipoIdTxt::canonicalize($Qid_tipo_activ);
}

// afecta a:
$PermUsuarioActividadRepository = DependencyResolver::get(PermUsuarioActividadRepositoryInterface::class);
foreach (PermAfectadosBits::map() as $afecta_a) {
    $aWhere = [
        'id_usuario' => $Qid_usuario,
        'dl_propia' => $Qdl_propia,
        'afecta_a' => $afecta_a,
    ];
    $aOperador = [];
    [$aWhere, $aOperador] = ActividadTipoIdTxt::applyToRepositoryWhere(
        $aWhere,
        $aOperador,
        $id_tipo_activ_txt,
    );

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
            $cPermUsuarioActividad = $PermUsuarioActividadRepository->getPermUsuarioActividades($aWhere, $aOperador);
            $oPermUsuarioActividad = null;
            $extras = [];
            foreach ($cPermUsuarioActividad as $rowPerm) {
                if ($oPermUsuarioActividad === null
                    || $rowPerm->getId_tipo_activ_txt() === $id_tipo_activ_txt
                ) {
                    if ($oPermUsuarioActividad !== null
                        && $oPermUsuarioActividad->getId_item() !== $rowPerm->getId_item()
                    ) {
                        $extras[] = $oPermUsuarioActividad;
                    }
                    $oPermUsuarioActividad = $rowPerm;
                } else {
                    $extras[] = $rowPerm;
                }
            }
            if ($oPermUsuarioActividad === null) {
                $newId_item = $PermUsuarioActividadRepository->getNewId();
                $oPermUsuarioActividad = new PermUsuarioActividad();
                $oPermUsuarioActividad->setId_item($newId_item);
            }
            $oPermUsuarioActividad->setId_usuario($Qid_usuario);
            $oPermUsuarioActividad->setId_tipo_activ_txt($id_tipo_activ_txt);
            $oPermUsuarioActividad->setDl_propia(\src\shared\domain\helpers\FuncTablasSupport::isTrue($Qdl_propia) === true);
            $oPermUsuarioActividad->setAfecta_a($afecta_a);
            $oPermUsuarioActividad->setFaseRefVo(is_numeric($fase_ref) ? (int) $fase_ref : null);
            $oPermUsuarioActividad->setPerm_on(is_numeric($perm_on) ? (int) $perm_on : null);
            $oPermUsuarioActividad->setPerm_off(is_numeric($perm_off) ? (int) $perm_off : null);
            if ($PermUsuarioActividadRepository->Guardar($oPermUsuarioActividad) === false) {
                $error_txt .= _("hay un error, no se ha guardado");
                $error_txt .= "\n" . $PermUsuarioActividadRepository->getErrorTxt();
            }
            foreach ($extras as $extra) {
                if ($extra->getId_item() === $oPermUsuarioActividad->getId_item()) {
                    continue;
                }
                $PermUsuarioActividadRepository->Eliminar($extra);
            }
            $eliminar = false;
        }
    }
    if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($eliminar)) {
        $cPermUsuarioActividad = $PermUsuarioActividadRepository->getPermUsuarioActividades($aWhere, $aOperador);
        foreach ($cPermUsuarioActividad as $oPermUsuarioActividad) {
            if ($PermUsuarioActividadRepository->Eliminar($oPermUsuarioActividad) === false) {
                $error_txt .= _("hay un error.");
                $error_txt .= "\n" . $PermUsuarioActividadRepository->getErrorTxt();
            }
        }
    }
}

ContestarJson::enviar($error_txt, 'ok');