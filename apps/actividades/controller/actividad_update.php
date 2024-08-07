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

use actividades\model\entity\Actividad;
use actividades\model\entity\ActividadAll;
use actividades\model\entity\ActividadDl;
use actividades\model\entity\ActividadEx;
use actividades\model\entity\Importada;
use actividadplazas\model\entity\ActividadPlazasDl;
use actividadplazas\model\entity\GestorActividadPlazas;
use core\ConfigGlobal;
use core\DBPropiedades;
use procesos\model\entity\GestorActividadProcesoTarea;

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
                $oActividad = new Actividad($id_activ);
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
        // si estoy creando una actividad de otra dl es porque la quiero importar y por tanto debe estar publicada.
        if ($Qdl_org != ConfigGlobal::mi_delef()) {
            $Qpublicado = 't';
            // comprobar que no es una dl que ya tiene su esquema
            $oDBPropiedades = new DBPropiedades();
            $a_posibles_esquemas = $oDBPropiedades->array_posibles_esquemas(TRUE, TRUE);
            $is_dl_in_orbix = FALSE;
            foreach ($a_posibles_esquemas as $esquema) {
                $row = explode('-', $esquema);
                if ($row[1] === $Qdl_org) {
                    $is_dl_in_orbix = TRUE;
                    break;
                }
            }
            if ($is_dl_in_orbix) {
                echo _("No puede crear una actividad que organiza una dl/r que ya usa aquinate");
                die();
            }

        }

        // Puede ser '000' > sin especificar
        $Qinom_tipo_val = (string)filter_input(INPUT_POST, 'inom_tipo_val');

        // permiso
        $_SESSION['oPermActividades']->setActividad($Qid_activ, $Qid_tipo_activ, $Qdl_org);
        // para dl y dlf:
        $dl_org_no_f = preg_replace('/(\.*)f$/', '\1', $Qdl_org);
        $dl_propia = (ConfigGlobal::mi_dele() == $dl_org_no_f) ? TRUE : FALSE;
        if (ConfigGlobal::is_app_installed('procesos') && $_SESSION['oPermActividades']->getPermisoCrear($dl_propia) === FALSE) {
            echo _("No tiene permiso para crear una actividad de este tipo") . "<br>";
            die();
        }

        //Compruebo que estén todos los campos necesasrios
        if (empty($Qnom_activ) || empty($Qf_ini) || empty($Qf_fin) || empty($Qstatus) || empty($Qdl_org)) {
            echo _("debe llenar todos los campos que tengan un (*)") . "<br>";
            die();
        }
        if (empty($Qinom_tipo_val)) {
            echo _("debe seleccionar un tipo de actividad") . "<br>";
            die();
        }

        $isfsv = substr($Qid_tipo_activ, 0, 1);
        $mi_dele = ConfigGlobal::mi_delef($isfsv);
        if ($Qdl_org == $mi_dele) {
            $oActividad = new ActividadDl();
        } else {
            $oActividad = new ActividadEx();
            $oActividad->setPublicado('t');
            $oActividad->setId_tabla('ex');
            $Qstatus = ActividadAll::STATUS_ACTUAL; // Que sea estado actual.
        }
        $oActividad->setDl_org($Qdl_org);
        if (isset($Qid_tipo_activ)) {
            if ($oActividad->setId_tipo_activ($Qid_tipo_activ) === false) {
                echo _("tipo de actividad incorrecto");
                die();
            }
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
        // Si nivel_stgr está vacio, pongo el calculado.
        if (empty($Qnivel_stgr)) {
            $Qnivel_stgr = $oActividad->generarNivelStgr();
        }
        $oActividad->setNivel_stgr($Qnivel_stgr);
        $oActividad->setId_repeticion($Qid_repeticion);
        $oActividad->setObserv_material($Qobserv_material);
        $oActividad->setTarifa($Qtarifa);
        $oActividad->setH_ini($Qh_ini);
        $oActividad->setH_fin($Qh_fin);
        $oActividad->setPublicado($Qpublicado);
        $oActividad->setPlazas($Qplazas);
        if ($oActividad->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $oActividad->getErrorTxt();
        }
        // si estoy creando una actividad de otra dl es porque la quiero importar.
        if ($Qdl_org != $mi_dele) {
            $id_activ = $oActividad->getId_activ();
            $oImportada = new Importada($id_activ);
            if ($oImportada->DBGuardar() === false) {
                echo _("hay un error, no se ha importado");
                echo "\n" . $oActividad->getErrorTxt();
            }
        }
        // Por defecto pongo todas las plazas en mi dl
        if (ConfigGlobal::is_app_installed('actividadplazas')) {
            if (!empty($Qplazas) && $Qdl_org == $mi_dele) {
                $id_activ = $oActividad->getId_activ();
                $id_dl = 0;
                $gesDelegacion = new ubis\model\entity\GestorDelegacion();
                $cDelegaciones = $gesDelegacion->getDelegaciones(array('dl' => $mi_dele));
                if (is_array($cDelegaciones) && count($cDelegaciones)) {
                    $id_dl = $cDelegaciones[0]->getId_dl();
                }
                //Si es la dl_org, son plazas concedidas, sino pedidas.
                $oActividadPlazasDl = new ActividadPlazasDl(array('id_activ' => $id_activ, 'id_dl' => $id_dl, 'dl_tabla' => $mi_dele));
                $oActividadPlazasDl->DBCarregar();
                $oActividadPlazasDl->setPlazas($Qplazas);

                //print_r($oActividadPlazasDl);
                if ($oActividadPlazasDl->DBGuardar() === false) {
                    echo _("hay un error, no se ha guardado");
                    echo "\n" . $oActividadPlazasDl->getErrorTxt();
                }
            }
        }
        break;
    case "duplicar": // duplicar la actividad.
        $a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if (!empty($a_sel)) {
            $id_activ = (integer)strtok($a_sel[0], '#');
            $oActividadAll = new Actividad($id_activ);
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
                $oActividad = new Actividad($id_activ);
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
            $oActividad = new Actividad($Qid_activ);
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
        //Aunque el content-type no sea un problema en la mayoría de casos, es recomendable especificarlo
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($jsondata);
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
        $oActividad = new Actividad($Qid_activ);
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
                echo _("debe seleccionar un tipo de actividad") . "<br>";
                die();
            }
        } else {
            $valor_id_tipo_activ = $Qid_tipo_activ;
        }


        $oActividad = new Actividad($Qid_activ);
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
            echo _("hay un error, no se ha guardado");
            echo "\n" . $oActividad->getErrorTxt();
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
                    $gesDelegacion = new ubis\model\entity\GestorDelegacion();
                    $cDelegaciones = $gesDelegacion->getDelegaciones(array('dl' => $mi_dele));
                    if (is_array($cDelegaciones) && count($cDelegaciones)) {
                        $id_dl = $cDelegaciones[0]->getId_dl();
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
                            echo _("hay un error, no se ha guardado");
                            echo "\n" . $oActividadPlazasDl->getErrorTxt();
                        }
                    }
                }
            }
        }
        break;
    default:
        $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
        exit ($err_switch);
} // fin del switch de mod.