<?php
/**
 * Endpoint backend AJAX: guarda la edicion de una actividad existente.
 * Si se cambia la delegacion de/ a la propia dl regenera el proceso, y propaga
 * `plazas` a la tabla de plazas de la propia dl cuando aplica.
 * Responde JSON {success, mensaje?}.
 *
 * Extraido del antiguo dispatcher actividad_update.php (case 'editar').
 *
 * @package    delegacion
 * @subpackage    actividades
 */

use core\ConfigGlobal;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadplazas\domain\contracts\ActividadPlazasDlRepositoryInterface;
use src\actividadplazas\domain\contracts\ActividadPlazasRepositoryInterface;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\Dinero;
use src\shared\domain\value_objects\TimeLocal;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\usuarios\domain\value_objects\IdLocale;
use web\ContestarJson;
use function core\is_true;

$Qid_activ = (integer)filter_input(INPUT_POST, 'id_activ');
$error_txt = '';

$Qid_tipo_activ = (integer)filter_input(INPUT_POST, 'id_tipo_activ');
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
$Qnum_asistentes = (integer)filter_input(INPUT_POST, 'num_asistentes');
$Qstatus = (integer)filter_input(INPUT_POST, 'status');
$Qid_repeticion = (integer)filter_input(INPUT_POST, 'id_repeticion');
$Qplazas = (integer)filter_input(INPUT_POST, 'plazas');
$Qtarifa = (integer)filter_input(INPUT_POST, 'id_tarifa');
$Qprecio = filter_input(INPUT_POST, 'precio', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$Qdl_org = (string)filter_input(INPUT_POST, 'dl_org');
$Qnom_activ = (string)filter_input(INPUT_POST, 'nom_activ');
$Qlugar_esp = (string)filter_input(INPUT_POST, 'lugar_esp');
$Qdesc_activ = (string)filter_input(INPUT_POST, 'desc_activ');
$Qf_ini = (string)filter_input(INPUT_POST, 'f_ini');
$Qf_fin = (string)filter_input(INPUT_POST, 'f_fin');
$Qobserv = (string)filter_input(INPUT_POST, 'observ');
$Qnivel_stgr = (integer)filter_input(INPUT_POST, 'nivel_stgr');
$Qobserv_material = (string)filter_input(INPUT_POST, 'observ_material');
$Qh_ini = (string)filter_input(INPUT_POST, 'h_ini');
$Qh_fin = (string)filter_input(INPUT_POST, 'h_fin');
$Qpublicado = (string)filter_input(INPUT_POST, 'publicado');
$Qidioma = (string)filter_input(INPUT_POST, 'idioma');

// Mirar si puedo cambiar el tipo de actividad:
$_SESSION['oPermActividades']->setActividad($Qid_activ, $Qid_tipo_activ, $Qdl_org);
$oPermActiv = $_SESSION['oPermActividades']->getPermisoActual('datos');
if (ConfigGlobal::is_app_installed('procesos') && $oPermActiv->have_perm_activ('crear') === TRUE) {
    $Qisfsv_val = (integer)filter_input(INPUT_POST, 'isfsv_val');
    $Qiasistentes_val = (integer)filter_input(INPUT_POST, 'iasistentes_val');
    $Qiactividad_val = (integer)filter_input(INPUT_POST, 'iactividad_val');
    // Puede ser '000' > sin especificar
    $Qinom_tipo_val = (string)filter_input(INPUT_POST, 'inom_tipo_val');
    $condta = $Qisfsv_val . $Qiasistentes_val . $Qiactividad_val . $Qinom_tipo_val;
    if (strpos($condta, '.') === false) {
        $valor_id_tipo_activ = $condta;
    } else {
        ContestarJson::enviar(_("debe seleccionar un tipo de actividad"));
        exit;
    }
} else {
    $valor_id_tipo_activ = $Qid_tipo_activ;
}

$ActividadDlRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
$oActividad = $ActividadDlRepository->findById($Qid_activ);
$plazas_old = $oActividad->getPlazas();
$dl_orig = $oActividad->getDl_org();

// compruebo que tiene 6 digitos
if (!empty($valor_id_tipo_activ) && !(($valor_id_tipo_activ / 100000) < 1)) {
    $oActividad->setId_tipo_activ($valor_id_tipo_activ);
}
if (isset($Qdl_org)) {
    $dl_org = strtok($Qdl_org, '#');
    $oActividad->setDl_org($dl_org);
} else {
    $dl_org = '';
    $oActividad->setDl_org('');
}
$oActividad->setNom_activ($Qnom_activ);

// En el caso de tener id_ubi (!=1) borro el campo lugar_esp.
if (!empty($Qid_ubi) && $Qid_ubi != 1) {
    $oActividad->setId_ubi($Qid_ubi);
    $oActividad->setLugar_esp('');
} else {
    $oActividad->setId_ubi($Qid_ubi);
    $oActividad->setLugar_esp($Qlugar_esp);
}
$oActividad->setDesc_activ($Qdesc_activ);
$oF_ini = empty($Qf_ini) ? null : DateTimeLocal::createFromLocal($Qf_ini);
$oActividad->setF_ini($oF_ini);
$oF_fin = empty($Qf_fin) ? null : DateTimeLocal::createFromLocal($Qf_fin);
$oActividad->setF_fin($oF_fin);
$Qprecio = empty($Qprecio) ? null : new Dinero($Qprecio);
$oActividad->setPrecioVo($Qprecio);
$oActividad->setNum_asistentes($Qnum_asistentes);
$oActividad->setStatus($Qstatus);
$oActividad->setObserv($Qobserv);
$oActividad->setNivel_stgr($Qnivel_stgr);
$oActividad->setId_repeticion($Qid_repeticion);
$oActividad->setObserv_material($Qobserv_material);
$oActividad->setTarifa($Qtarifa);
$oH_ini = empty($Qh_ini) ? null : TimeLocal::fromString($Qh_ini);
$oActividad->setH_ini($oH_ini);
$oH_fin = empty($Qh_fin) ? null : TimeLocal::fromString($Qh_fin);
$oActividad->setH_fin($oH_fin);
$oActividad->setPublicado(is_true($Qpublicado));
$oActividad->setPlazas($Qplazas);
$Qidioma = empty($Qidioma) ? null : new IdLocale($Qidioma);
$oActividad->setIdiomaVo($Qidioma);
if ($ActividadDlRepository->Guardar($oActividad) === false) {
    $error_txt .= _("hay un error, no se ha guardado");
    $error_txt .= "\n" . $oActividad->getErrorTxt();
} else {
    // Si cambio de dl_propia a otra (o al reves), hay que cambiar el proceso.
    if (ConfigGlobal::is_app_installed('procesos')) {
        if (($dl_orig != $dl_org) && ($dl_org == ConfigGlobal::mi_delef() || $dl_orig == ConfigGlobal::mi_delef())) {
            $ActividadProcesoTareaRepository = $GLOBALS['container']->get(ActividadProcesoTareaRepositoryInterface::class);
            $ActividadProcesoTareaRepository->generarProceso($oActividad->getId_activ());
        }
    }
    // Por defecto pongo todas las plazas en mi dl
    if (ConfigGlobal::is_app_installed('actividadplazas')) {
        $mi_dele = ConfigGlobal::mi_delef();
        if (!empty($Qplazas) && ($plazas_old != $Qplazas) && $Qdl_org == $mi_dele) {
            $id_dl = 0;
            $repoDelegacion = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
            $cDelegaciones = $repoDelegacion->getDelegaciones(['dl' => $mi_dele]);
            if (is_array($cDelegaciones) && count($cDelegaciones)) {
                $id_dl = $cDelegaciones[0]->getIdDlVo()?->value() ?? 0;
            }
            $ActividadPlazasRepository = $GLOBALS['container']->get(ActividadPlazasRepositoryInterface::class);
            $aWhere = [
                'id_activ' => $Qid_activ,
                'id_dl' => $id_dl,
                'dl_tabla' => $mi_dele,
            ];
            $cActividadPlazas = $ActividadPlazasRepository->getactividadesPlazas($aWhere);
            $salta = 0;
            if (count($cActividadPlazas) === 1) {
                $oActividadPlazasDl = $cActividadPlazas[0];
                $plazas_dl = $oActividadPlazasDl->getPlazas();
                if ($plazas_dl !== $plazas_old) {
                    $salta = 1;
                }
            }
            if ($salta !== 1) {
                $ActividadPlazasDlRepository = $GLOBALS['container']->get(ActividadPlazasDlRepositoryInterface::class);
                $oActividadPlazasDl = $ActividadPlazasRepository->findById($Qid_activ);
                $oActividadPlazasDl->setPlazas($Qplazas);

                if ($ActividadPlazasDlRepository->Guardar($oActividadPlazasDl) === false) {
                    $error_txt .= _("hay un error, no se ha guardado");
                    $error_txt .= "\n" . $oActividadPlazasDl->getErrorTxt();
                }
            }
        }
    }
}

ContestarJson::enviar($error_txt);
