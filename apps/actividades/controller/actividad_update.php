<?php
/**
 * Esta página actualiza la tabla de las actividades.
 *
 *
 * @param string $mod 'nuevo'|'cambiar_tipo'|'eliminar'|'editar'|'actualizar_sacd'|'actualizar_ctr'
 * @param string $origen 'calendario' sirve para volver (si no es calendario).
 * @author    Daniel Serrabou
 * @since        15/5/02.
 *
 *
 * @package    delegacion
 * @subpackage    actividades
 */

use actividades\domain\ActividadNueva;
use actividades\model\entity\Actividad;
use actividades\model\entity\ActividadAll;
use actividades\model\entity\ActividadDl;
use actividades\model\entity\Importada;
use actividadplazas\model\entity\ActividadPlazasDl;
use actividadplazas\model\entity\GestorActividadPlazas;
use core\ConfigGlobal;
use Illuminate\Http\JsonResponse;
use procesos\model\entity\GestorActividadProcesoTarea;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

/**
 * Para asegurar que inicia la sesion, y poder acceder a los permisos
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

function borrar_actividad($id_activ)
{
    $oActividad = new Actividad($id_activ);
    $oActividad->DBCarregar();
    $dl_org = $oActividad->getDl_org();
    $id_tabla = $oActividad->getId_tabla();
    // para des => dl y dlf:
    $dl_org_no_f = preg_replace('/(\.*)f$/', '\1', $dl_org);
    $dl_propia = (ConfigGlobal::mi_dele() == $dl_org_no_f) ? TRUE : FALSE;

    if ($dl_propia) { // de la propia dl
        $status = $oActividad->getStatus();
        if (!empty($status) && $status == 1) { // si no esta en proyecto (status=1) no dejo borrar,
            if ($oActividad->DBEliminar() === false) {
                echo _("hay un error, no se ha eliminado");
                echo "\n" . $oActividad->getErrorTxt();
            }
        } else {
            $oActividad->setStatus(4); // la pongo en estado borrable
            if ($oActividad->DBGuardar() === false) {
                echo _("hay un error, no se ha guardado");
                echo "\n" . $oActividad->getErrorTxt();
            }
        }
    } else {
        if ($id_tabla === 'dl') {
            // No se puede eliminar una actividad de otra dl. Hay que borrarla como importada
            $oImportada = new Importada($id_activ);
            $oImportada->DBEliminar();
        } else { // de otras dl en resto
            $oActividad->setStatus(4); // la pongo en estado borrable
            if ($oActividad->DBGuardar() === false) {
                echo _("hay un error, no se ha guardado");
                echo "\n" . $oActividad->getErrorTxt();
            }
        }
    }
}

$Qid_activ = (integer)filter_input(INPUT_POST, 'id_activ');
$Qmod = (string)filter_input(INPUT_POST, 'mod');

switch ($Qmod) {
    case 'publicar':
        $a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if (!empty($a_sel)) { // puedo seleccionar más de uno.
            foreach ($a_sel as $id) {
                $id_activ = (integer)strtok($id, '#');
                $oActividad = new ActividadAll($id_activ);
                $oActividad->DBCarregar();
                $oActividad->setPublicado('t');
                if ($oActividad->DBGuardar() === false) {
                    echo _("hay un error, no se ha guardado");
                    echo "\n" . $oActividad->getErrorTxt();
                    $err = 1;
                }
            }
        }
        break;
    case 'importar':
        $a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if (!empty($a_sel)) { // puedo seleccionar más de uno.
            foreach ($a_sel as $id) {
                $id_activ = (integer)strtok($id, '#');
                $oImportada = new Importada($id_activ);
                if ($oImportada->DBGuardar() === false) {
                    echo _("hay un error, no se ha importado");
                    echo "\n" . $oImportada->getErrorTxt();
                }
                // generar proceso.
                if (ConfigGlobal::is_app_installed('procesos')) {
                    $oGestorActividadProcesoTarea = new GestorActividadProcesoTarea();
                    $oGestorActividadProcesoTarea->generarProceso($id_activ, ConfigGlobal::mi_sfsv(), TRUE);
                }
            }
        }
        break;
    case "nuevo":
        // Puede ser '000' > sin especificar
        $Qinom_tipo_val = (string)filter_input(INPUT_POST, 'inom_tipo_val');
        if (empty($Qinom_tipo_val)) {
            echo _("debe seleccionar un tipo de actividad") . "<br>";
            die();
        }

        $datosActividad = [
            'id_tipo_activ' => (integer)filter_input(INPUT_POST, 'id_tipo_activ'),
            'id_ubi' => (integer)filter_input(INPUT_POST, 'id_ubi'),
            'num_asistentes' => (integer)filter_input(INPUT_POST, 'num_asistentes'),
            'status' => (integer)filter_input(INPUT_POST, 'status'),
            'id_repeticion' => (integer)filter_input(INPUT_POST, 'id_repeticion'),
            'plazas' => (integer)filter_input(INPUT_POST, 'plazas'),
            'tarifa' => (integer)filter_input(INPUT_POST, 'id_tarifa'),
            'precio' => filter_input(INPUT_POST, 'precio', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),

            'dl_org' => (string)filter_input(INPUT_POST, 'dl_org'),
            'nom_activ' => (string)filter_input(INPUT_POST, 'nom_activ'),
            'lugar_esp' => (string)filter_input(INPUT_POST, 'lugar_esp'),
            'desc_activ' => (string)filter_input(INPUT_POST, 'desc_activ'),
            'f_ini' => (string)filter_input(INPUT_POST, 'f_ini'),
            'f_fin' => (string)filter_input(INPUT_POST, 'f_fin'),
            'tipo_horario' => (string)filter_input(INPUT_POST, 'tipo_horario'),
            'observ' => (string)filter_input(INPUT_POST, 'observ'),
            'nivel_stgr' => (string)filter_input(INPUT_POST, 'nivel_stgr'),
            'observ_material' => (string)filter_input(INPUT_POST, 'observ_material'),
            'h_ini' => (string)filter_input(INPUT_POST, 'h_ini'),
            'h_fin' => (string)filter_input(INPUT_POST, 'h_fin'),
            'publicado' => (string)filter_input(INPUT_POST, 'publicado'),
        ];

        $error_txt = ActividadNueva::actividadNueva($datosActividad);

        if (!empty($error_txt)) {
            $jsondata['success'] = FALSE;
            $jsondata['mensaje'] = $error_txt;
        } else {
            $jsondata['success'] = TRUE;
        }
        (new JsonResponse($jsondata))->send();
        break;
    case "duplicar": // duplicar la actividad.
        $a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if (!empty($a_sel)) {
            $id_activ = (integer)strtok($a_sel[0], '#');
            $oActividadAll = new ActividadAll($id_activ);
            $dl = $oActividadAll->getDl_org();
            // des si puede duplicar sf.
            if ($dl == ConfigGlobal::mi_delef() ||
                ($_SESSION['oPerm']->have_perm_oficina('des') && $dl == ConfigGlobal::mi_dele() . 'f')
            ) {
                $oActividad = new ActividadDl($id_activ);
            } else {
                exit(_("no se puede duplicar actividades que no sean de la propia dl"));
            }
            $oActividad->DBCarregar();
            $oActividad->setId_activ('0'); //para que al guardar genere un nuevo id.
            $nom = _("dup") . ' ' . $oActividad->getNom_activ();
            $oActividad->setNom_activ($nom);
            $oActividad->setStatus(1); // la pongo en estado proyecto
            if ($oActividad->DBGuardar() === false) {
                echo _("hay un error, no se ha guardado");
                echo "\n" . $oActividad->getErrorTxt();
            }
            $oActividad->DBCarregar();
        }
        break;
    case "eliminar": // Eliminar la actividad.
        $error_txt = '';
        $a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if (!empty($a_sel)) { // puedo seleccionar más de uno.
            foreach ($a_sel as $id) {
                $id_activ = (integer)strtok($id, '#');
                $oActividad = new ActividadAll($id_activ);
                $id_tipo_activ = $oActividad->getId_tipo_activ();
                $dl_org = $oActividad->getDl_org();

                if (ConfigGlobal::is_app_installed('procesos')) {
                    $_SESSION['oPermActividades']->setActividad($id_activ, $id_tipo_activ, $dl_org);
                    $oPermActiv = $_SESSION['oPermActividades']->getPermisoActual('datos');
                    if ($oPermActiv->have_perm_activ('borrar') === TRUE) {
                        borrar_actividad($id_activ);
                    } else {
                        $error_txt .= _("No tiene permiso para borrar esta actividad");
                    }
                } else {
                    borrar_actividad($id_activ);
                }
            }
        }
        // si vengo desde la presentación del planning, ya tengo el id_activ.
        if (!empty($Qid_activ)) {
            $oActividad = new ActividadAll($Qid_activ);
            $id_tipo_activ = $oActividad->getId_tipo_activ();
            $dl_org = $oActividad->getDl_org();

            if (ConfigGlobal::is_app_installed('procesos')) {
                $_SESSION['oPermActividades']->setActividad($Qid_activ, $id_tipo_activ, $dl_org);
                $oPermActiv = $_SESSION['oPermActividades']->getPermisoActual('datos');
                if ($oPermActiv->have_perm_activ('borrar') === TRUE) {
                    borrar_actividad($id_activ);
                } else {
                    $error_txt .= _("No tiene permiso para borrar esta actividad");
                }
            } else {
                borrar_actividad($id_activ);
            }
        }

        if (!empty($error_txt)) {
            $jsondata['success'] = FALSE;
            $jsondata['mensaje'] = $error_txt;
        } else {
            $jsondata['success'] = TRUE;
        }
        (new JsonResponse($jsondata))->send();
        break;
    case "cambiar_tipo": // sólo cambio el tipo a una actividad existente //____________________________
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
        $Qtipo_horario = (string)filter_input(INPUT_POST, 'tipo_horario');
        $Qobserv = (string)filter_input(INPUT_POST, 'observ');
        $Qnivel_stgr = (string)filter_input(INPUT_POST, 'nivel_stgr');
        $Qobserv_material = (string)filter_input(INPUT_POST, 'observ_material');
        $Qh_ini = (string)filter_input(INPUT_POST, 'h_ini');
        $Qh_fin = (string)filter_input(INPUT_POST, 'h_fin');
        $Qpublicado = (string)filter_input(INPUT_POST, 'publicado');

        //echo "id_tipo de actividad: $id_tipo_activ<br>";
        if (!empty($Qid_tipo_activ) and strpos($Qid_tipo_activ, '.') === false) {
            $valor_id_tipo_activ = $Qid_tipo_activ;
        } else {
            $condta = $Qisfsv_val . $Qiasistentes_val . $Qiactividad_val . $Qinom_tipo_val;
            if (strpos($condta, '.') === false) {
                $valor_id_tipo_activ = $condta;
            } else {
                echo _("debe seleccionar un tipo de actividad") . "<br>";
                die();
            }
        }
        $oActividad = new ActividadAll($Qid_activ);
        $oActividad->DBCarregar();
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
        $oActividad->setF_ini($Qf_ini);
        $oActividad->setF_fin($Qf_fin);
        $oActividad->setTipo_horario($Qtipo_horario);
        $oActividad->setPrecio($Qprecio);
        $oActividad->setNum_asistentes($Qnum_asistentes);
        $oActividad->setStatus($Qstatus);
        $oActividad->setObserv($Qobserv);
        $oActividad->setNivel_stgr($Qnivel_stgr);
        $oActividad->setId_repeticion($Qid_repeticion);
        $oActividad->setObserv_material($Qobserv_material);
        $oActividad->setLugar_esp($Qlugar_esp);
        $oActividad->setTarifa($Qtarifa);
        $oActividad->setH_ini($Qh_ini);
        $oActividad->setH_fin($Qh_fin);
        $oActividad->setPlazas($Qplazas);
        if ($oActividad->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $oActividad->getErrorTxt();
        }
        // Si tiene procesos, hay que hacerlo de nuevo
        if (ConfigGlobal::is_app_installed('procesos')) {
            // Copiado de actividad_proceso_ajax case 'generar':
            $oGestorActividadProcesoTarea = new GestorActividadProcesoTarea();
            $oGestorActividadProcesoTarea->generarProceso($Qid_activ, ConfigGlobal::mi_sfsv(), TRUE);
        }
        break;
    case "editar": // editar la actividad.
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
        $Qtipo_horario = (string)filter_input(INPUT_POST, 'tipo_horario');
        $Qobserv = (string)filter_input(INPUT_POST, 'observ');
        $Qnivel_stgr = (string)filter_input(INPUT_POST, 'nivel_stgr');
        $Qobserv_material = (string)filter_input(INPUT_POST, 'observ_material');
        $Qh_ini = (string)filter_input(INPUT_POST, 'h_ini');
        $Qh_fin = (string)filter_input(INPUT_POST, 'h_fin');
        $Qpublicado = (string)filter_input(INPUT_POST, 'publicado');

        // Mirar si puedo cambiar el tipo de actividad:
        // permiso
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
                $error_txt = _("debe seleccionar un tipo de actividad") . "<br>";
                die($error_txt);
            }
        } else {
            $valor_id_tipo_activ = $Qid_tipo_activ;
        }


        $oActividad = new ActividadAll($Qid_activ);
        $oActividad->DBCarregar();
        $plazas_old = $oActividad->getPlazas();

        // compruebo que tiene 6 dígitos
        if (!empty($valor_id_tipo_activ) && !(($valor_id_tipo_activ / 100000) < 1)) {
            $oActividad->setId_tipo_activ($valor_id_tipo_activ);
        }
        if (isset($Qdl_org)) {
            $dl_orig = $oActividad->getDl_org();
            $dl_org = strtok($Qdl_org, '#');
            $oActividad->setDl_org($dl_org);
        } else {
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
        $oActividad->setF_ini($Qf_ini);
        $oActividad->setF_fin($Qf_fin);
        $oActividad->setTipo_horario($Qtipo_horario);
        $oActividad->setPrecio($Qprecio);
        $oActividad->setNum_asistentes($Qnum_asistentes);
        $oActividad->setStatus($Qstatus);
        $oActividad->setObserv($Qobserv);
        $oActividad->setNivel_stgr($Qnivel_stgr);
        $oActividad->setId_repeticion($Qid_repeticion);
        $oActividad->setObserv_material($Qobserv_material);
        $oActividad->setTarifa($Qtarifa);
        $oActividad->setH_ini($Qh_ini);
        $oActividad->setH_fin($Qh_fin);
        $oActividad->setPublicado($Qpublicado);
        $oActividad->setPlazas($Qplazas);
        if ($oActividad->DBGuardar() === false) {
            $error_txt .= _("hay un error, no se ha guardado");
            $error_txt .= "\n" . $oActividad->getErrorTxt();
        } else {
            // Si cambio de dl_propia a otra (o al revés), hay que cambiar el proceso. Se hace al final para que la actividad ya tenga puesta la nueva dl
            if (ConfigGlobal::is_app_installed('procesos')) {
                if (($dl_orig != $dl_org) && ($dl_org == ConfigGlobal::mi_delef() || $dl_orig == ConfigGlobal::mi_delef())) {
                    $oGestorActividadProcesoTarea = new GestorActividadProcesoTarea();
                    $oGestorActividadProcesoTarea->generarProceso($oActividad->getId_activ());
                }
            }
            // Por defecto pongo todas las plazas en mi dl
            if (ConfigGlobal::is_app_installed('actividadplazas')) {
                $mi_dele = ConfigGlobal::mi_delef();
                if (!empty($Qplazas) && ($plazas_old != $Qplazas) && $Qdl_org == $mi_dele) {
                    $id_dl = 0;
                    $repoDelegacion = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
                    $cDelegaciones = $repoDelegacion->getDelegaciones(array('dl' => $mi_dele));
                    if (is_array($cDelegaciones) && count($cDelegaciones)) {
                        $id_dl = $cDelegaciones[0]->getIdDlVo()?->value() ?? 0;
                    }
                    // si ya tengo algo, mejor no toco. (a no ser que tenga todas)
                    $oGesActividadPlazas = new GestorActividadPlazas();
                    $aWhere = [];
                    $aWhere['id_activ'] = $Qid_activ;
                    $aWhere['id_dl'] = $id_dl;
                    $aWhere['dl_tabla'] = $mi_dele;
                    $cActividadPlazas = $oGesActividadPlazas->getactividadesPlazas($aWhere);
                    $salta = 0;
                    if (count($cActividadPlazas) == 1) {
                        $oActividadPlazasDl = $cActividadPlazas[0];
                        $plazas_dl = $oActividadPlazasDl->getPlazas();
                        if ($plazas_dl != $plazas_old) {
                            $salta = 1;
                        }
                    }
                    if ($salta != 1) {
                        //Si es la dl_org, son plazas concedidas, sino pedidas.
                        $oActividadPlazasDl = new ActividadPlazasDl($aWhere);
                        $oActividadPlazasDl->DBCarregar();
                        $oActividadPlazasDl->setPlazas($Qplazas);

                        if ($oActividadPlazasDl->DBGuardar() === false) {
                            $error_txt .= _("hay un error, no se ha guardado");
                            $error_txt .= "\n" . $oActividadPlazasDl->getErrorTxt();
                        }
                    }
                }
            }
        }

        if (!empty($error_txt)) {
            $jsondata['success'] = FALSE;
            $jsondata['mensaje'] = $error_txt;
        } else {
            $jsondata['success'] = TRUE;
        }
        (new JsonResponse($jsondata))->send();
        break;
    default:
        $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
        exit ($err_switch);
} // fin del switch de mod.