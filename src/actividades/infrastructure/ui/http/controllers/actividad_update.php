<?php
/**
 * Endpoint backend AJAX para crear/editar/eliminar/duplicar/publicar/importar/cambiar_tipo
 * actividades. Despacha segun el parametro POST 'mod'.
 *
 * Migrado desde frontend/actividades/controller/actividad_update.php (que a su
 * vez se habia migrado desde frontend/actividades/controller/actividad_update.php).
 * Esta version vive en la capa backend porque toda su logica es acceso a
 * repositorios y casos de uso del dominio; no hay presentacion.
 *
 * @param string $mod 'nuevo'|'cambiar_tipo'|'eliminar'|'editar'|'publicar'|'importar'|'duplicar'
 *
 * @package    delegacion
 * @subpackage    actividades
 */

use core\ConfigGlobal;
use Illuminate\Http\JsonResponse;
use src\actividades\application\ActividadNueva;
use src\actividades\application\BorrarActividad;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\contracts\ImportadaRepositoryInterface;
use src\actividades\domain\entity\Importada;
use src\actividades\domain\value_objects\StatusId;
use src\actividadplazas\domain\contracts\ActividadPlazasDlRepositoryInterface;
use src\actividadplazas\domain\contracts\ActividadPlazasRepositoryInterface;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\Dinero;
use src\shared\domain\value_objects\TimeLocal;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\usuarios\domain\value_objects\IdLocale;
use function core\is_true;

$Qid_activ = (integer)filter_input(INPUT_POST, 'id_activ');
$Qmod = (string)filter_input(INPUT_POST, 'mod');

switch ($Qmod) {
    case 'publicar':
        $a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if (!empty($a_sel)) {
            $ActividadDlRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
            foreach ($a_sel as $id) {
                $id_activ = (integer)strtok($id, '#');
                $oActividad = $ActividadDlRepository->findById($id_activ);
                $oActividad->setPublicado('t');
                if ($ActividadDlRepository->Guardar($oActividad) === false) {
                    echo _("hay un error, no se ha guardado");
                    echo "\n" . $oActividad->getErrorTxt();
                }
            }
        }
        break;
    case 'importar':
        $a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if (!empty($a_sel)) {
            $ImportadaRepository = $GLOBALS['container']->get(ImportadaRepositoryInterface::class);
            foreach ($a_sel as $id) {
                $id_activ = (integer)strtok($id, '#');
                $oImportada = new Importada();
                $oImportada->setId_activ($id_activ);
                if ($ImportadaRepository->Guardar($oImportada) === false) {
                    echo _("hay un error, no se ha importado");
                    echo "\n" . $ImportadaRepository->getErrorTxt();
                }
                if (ConfigGlobal::is_app_installed('procesos')) {
                    $ActividadProcesoTareaRepository = $GLOBALS['container']->get(ActividadProcesoTareaRepositoryInterface::class);
                    $ActividadProcesoTareaRepository->generarProceso($id_activ, ConfigGlobal::mi_sfsv(), TRUE);
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
            'idioma' => (string)filter_input(INPUT_POST, 'idioma'),
            'observ_material' => (string)filter_input(INPUT_POST, 'observ_material'),
            'h_ini' => (string)filter_input(INPUT_POST, 'h_ini'),
            'h_fin' => (string)filter_input(INPUT_POST, 'h_fin'),
            'publicado' => (string)filter_input(INPUT_POST, 'publicado'),
        ];

        try {
            ActividadNueva::actividadNueva($datosActividad);
            $jsondata = ['success' => TRUE];
        } catch (Exception $e) {
            $jsondata = ['success' => FALSE, 'mensaje' => $e->getMessage()];
        }

        (new JsonResponse($jsondata))->send();
        break;
    case "duplicar":
        $a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if (!empty($a_sel)) {
            $ActividadDlRepository = $GLOBALS['container']->get(ActividadDlRepositoryInterface::class);
            $id_activ = (integer)strtok($a_sel[0], '#');
            $oActividadAll = $ActividadDlRepository->findById($id_activ);
            $dl = $oActividadAll->getDl_org();
            // des si puede duplicar sf.
            if ($dl === ConfigGlobal::mi_delef() ||
                ($_SESSION['oPerm']->have_perm_oficina('des') && $dl === ConfigGlobal::mi_dele() . 'f')
            ) {
                $oActividad = $ActividadDlRepository->findById($id_activ);
                $newId = $ActividadDlRepository->getNewId();
                $newIdActiv = $ActividadDlRepository->getNewIdActividad($newId);
            } else {
                exit(_("no se puede duplicar actividades que no sean de la propia dl"));
            }
            $oActividad->setId_activ($newIdActiv);
            $nom = _("dup") . ' ' . $oActividad->getNom_activ();
            $oActividad->setNom_activ($nom);
            $oActividad->setStatus(StatusId::PROYECTO);
            $ActividadDlRepository->Guardar($oActividad);
        }
        break;
    case "eliminar":
        $error_txt = '';
        $a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $ActividadDlRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        if (!empty($a_sel)) {
            foreach ($a_sel as $id) {
                $id_activ = (integer)strtok($id, '#');
                $oActividad = $ActividadDlRepository->findById($id_activ);
                $id_tipo_activ = $oActividad->getId_tipo_activ();
                $dl_org = $oActividad->getDl_org();

                if (ConfigGlobal::is_app_installed('procesos')) {
                    $_SESSION['oPermActividades']->setActividad($id_activ, $id_tipo_activ, $dl_org);
                    $oPermActiv = $_SESSION['oPermActividades']->getPermisoActual('datos');
                    if ($oPermActiv->have_perm_activ('borrar') === TRUE) {
                        $error_txt .= BorrarActividad::ejecutar($id_activ);
                    } else {
                        $error_txt .= _("No tiene permiso para borrar esta actividad");
                    }
                } else {
                    $error_txt .= BorrarActividad::ejecutar($id_activ);
                }
            }
        }
        // si vengo desde la presentacion del planning, ya tengo el id_activ.
        if (!empty($Qid_activ)) {
            $oActividad = $ActividadDlRepository->findById($Qid_activ);
            $id_tipo_activ = $oActividad->getId_tipo_activ();
            $dl_org = $oActividad->getDl_org();

            if (ConfigGlobal::is_app_installed('procesos')) {
                $_SESSION['oPermActividades']->setActividad($Qid_activ, $id_tipo_activ, $dl_org);
                $oPermActiv = $_SESSION['oPermActividades']->getPermisoActual('datos');
                if ($oPermActiv->have_perm_activ('borrar') === TRUE) {
                    $error_txt .= BorrarActividad::ejecutar($Qid_activ);
                } else {
                    $error_txt .= _("No tiene permiso para borrar esta actividad");
                }
            } else {
                $error_txt .= BorrarActividad::ejecutar($Qid_activ);
            }
        }

        if (!empty($error_txt)) {
            $jsondata = ['success' => FALSE, 'mensaje' => $error_txt];
        } else {
            $jsondata = ['success' => TRUE];
        }
        (new JsonResponse($jsondata))->send();
        break;
    case "cambiar_tipo":
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
                echo _("debe seleccionar un tipo de actividad") . "<br>";
                die();
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
        $Qprecio = empty($Qprecio) ? null : new Dinero($Qprecio);
        $oActividad->setPrecio($Qprecio);
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
        $ActividadDlRepository->Guardar($oActividad);
        if (ConfigGlobal::is_app_installed('procesos')) {
            $ActividadProcesoTareaRepository = $GLOBALS['container']->get(ActividadProcesoTareaRepositoryInterface::class);
            $ActividadProcesoTareaRepository->generarProceso($Qid_activ, ConfigGlobal::mi_sfsv(), TRUE);
        }
        break;
    case "editar":
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
                $error_txt = _("debe seleccionar un tipo de actividad") . "<br>";
                die($error_txt);
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

        if (!empty($error_txt)) {
            $jsondata = ['success' => FALSE, 'mensaje' => $error_txt];
        } else {
            $jsondata = ['success' => TRUE];
        }
        (new JsonResponse($jsondata))->send();
        break;
    default:
        $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
        exit($err_switch);
}
