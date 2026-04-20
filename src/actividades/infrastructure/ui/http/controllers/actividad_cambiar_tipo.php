<?php
/**
 * Endpoint backend AJAX: cambia el tipo de una actividad existente.
 * Regenera el proceso asociado si la app `procesos` esta instalada.
 * Responde JSON {success, mensaje?}.
 *
 * Extraido del antiguo dispatcher actividad_update.php (case 'cambiar_tipo').
 *
 * @package    delegacion
 * @subpackage    actividades
 */

use core\ConfigGlobal;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\Dinero;
use src\shared\domain\value_objects\TimeLocal;
use web\ContestarJson;

$error_txt = '';
$Qid_activ = (integer)filter_input(INPUT_POST, 'id_activ');
$Qid_tipo_activ = (integer)filter_input(INPUT_POST, 'id_tipo_activ');
$Qisfsv_val = (integer)filter_input(INPUT_POST, 'isfsv_val');
$Qiasistentes_val = (integer)filter_input(INPUT_POST, 'iasistentes_val');
$Qiactividad_val = (integer)filter_input(INPUT_POST, 'iactividad_val');
// Puede ser '000' > sin especificar
$Qinom_tipo_val = (string)filter_input(INPUT_POST, 'inom_tipo_val');

$Qdl_org = (string)filter_input(INPUT_POST, 'dl_org');
$Qnum_asistentes = (integer)filter_input(INPUT_POST, 'num_asistentes');
$Qstatus = (integer)filter_input(INPUT_POST, 'status');
$Qid_repeticion = (integer)filter_input(INPUT_POST, 'id_repeticion');
$Qplazas = (integer)filter_input(INPUT_POST, 'plazas');
$Qtarifa = (integer)filter_input(INPUT_POST, 'id_tarifa');
$Qprecio = filter_input(INPUT_POST, 'precio', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$Qnom_activ = (string)filter_input(INPUT_POST, 'nom_activ');
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
$Qlugar_esp = (string)filter_input(INPUT_POST, 'lugar_esp');
$Qdesc_activ = (string)filter_input(INPUT_POST, 'desc_activ');
$Qf_ini = (string)filter_input(INPUT_POST, 'f_ini');
$Qf_fin = (string)filter_input(INPUT_POST, 'f_fin');
$Qobserv = (string)filter_input(INPUT_POST, 'observ');
$Qnivel_stgr = (integer)filter_input(INPUT_POST, 'nivel_stgr');
$Qobserv_material = (string)filter_input(INPUT_POST, 'observ_material');
$Qh_ini = (string)filter_input(INPUT_POST, 'h_ini');
$Qh_fin = (string)filter_input(INPUT_POST, 'h_fin');

if (!empty($Qid_tipo_activ) && strpos($Qid_tipo_activ, '.') === false) {
    $valor_id_tipo_activ = $Qid_tipo_activ;
} else {
    $condta = $Qisfsv_val . $Qiasistentes_val . $Qiactividad_val . $Qinom_tipo_val;
    if (strpos($condta, '.') === false) {
        $valor_id_tipo_activ = $condta;
    } else {
        ContestarJson::enviar(_("debe seleccionar un tipo de actividad"));
        exit;
    }
}
$ActividadDlRepository = $GLOBALS['container']->get(ActividadDlRepositoryInterface::class);
$oActividad = $ActividadDlRepository->findById($Qid_activ);
$oActividad->setId_tipo_activ($valor_id_tipo_activ);
if (isset($Qdl_org)) {
    $dl_org = strtok($Qdl_org, '#');
    $oActividad->setDl_org($dl_org);
} else {
    $oActividad->setDl_org('');
}
$oActividad->setNom_activ($Qnom_activ);
$oActividad->setId_ubi($Qid_ubi);
$oActividad->setDesc_activ($Qdesc_activ);
$oF_ini = empty($Qf_ini) ? null : DateTimeLocal::createFromLocal($Qf_ini);
$oActividad->setF_ini($oF_ini);
$oF_fin = empty($Qf_fin) ? null : DateTimeLocal::createFromLocal($Qf_fin);
$oActividad->setF_fin($oF_fin);
$precioVo = ($Qprecio === false || $Qprecio === null || $Qprecio === '')
    ? null
    : new Dinero($Qprecio);
$oActividad->setPrecioVo($precioVo);
$oActividad->setNum_asistentes($Qnum_asistentes);
$oActividad->setStatus($Qstatus);
$oActividad->setObserv($Qobserv);
$oActividad->setNivel_stgr($Qnivel_stgr);
$oActividad->setId_repeticion($Qid_repeticion);
$oActividad->setObserv_material($Qobserv_material);
$oActividad->setLugar_esp($Qlugar_esp);
$oActividad->setTarifa($Qtarifa);
$oH_ini = empty($Qh_ini) ? null : TimeLocal::fromString($Qh_ini);
$oActividad->setH_ini($oH_ini);
$oH_fin = empty($Qh_fin) ? null : TimeLocal::fromString($Qh_fin);
$oActividad->setH_fin($oH_fin);
$oActividad->setPlazas($Qplazas);
if ($ActividadDlRepository->Guardar($oActividad) === false) {
    $error_txt .= _("hay un error, no se ha guardado");
    $error_txt .= "\n" . $ActividadDlRepository->getErrorTxt();
} else {
    if (ConfigGlobal::is_app_installed('procesos')) {
        $ActividadProcesoTareaRepository = $GLOBALS['container']->get(ActividadProcesoTareaRepositoryInterface::class);
        $ActividadProcesoTareaRepository->generarProceso($Qid_activ, ConfigGlobal::mi_sfsv(), TRUE);
    }
}

ContestarJson::enviar($error_txt);
