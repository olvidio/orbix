<?php

namespace actividades\domain;

use actividades\model\entity\ActividadAll;
use actividades\model\entity\ActividadDl;
use actividades\model\entity\ActividadEx;
use actividades\model\entity\Importada;
use actividadplazas\model\entity\ActividadPlazasDl;
use core\ConfigGlobal;
use core\DBPropiedades;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use ubis;

class ActividadNueva
{

    /**
     * @param string $Qdl_org
     * @param string $Qpublicado
     * @param int $Qid_tipo_activ
     * @param string $Qnom_activ
     * @param string $Qf_ini
     * @param string $Qf_fin
     * @param int $Qstatus
     * @param int $Qid_ubi
     * @param string $Qlugar_esp
     * @param string $Qdesc_activ
     * @param string $Qtipo_horario
     * @param mixed $Qprecio
     * @param int $Qnum_asistentes
     * @param string $Qobserv
     * @param string $Qnivel_stgr
     * @param int $Qid_repeticion
     * @param string $Qobserv_material
     * @param int $Qtarifa
     * @param string $Qh_ini
     * @param string $Qh_fin
     * @param int $Qplazas
     * @return string
     */
    public static function actividadNueva(array $datosActividad): string
    {
        $error_txt = '';

        $Qdl_org = $datosActividad['dl_org'];
        $Qpublicado = $datosActividad['publicado'];
        $Qid_tipo_activ = $datosActividad['id_tipo_activ'];
        $Qnom_activ = $datosActividad['nom_activ'];
        $Qf_ini = $datosActividad['f_ini'];
        $Qf_fin = $datosActividad['f_fin'];
        $Qstatus = $datosActividad['status'];
        $Qid_ubi = $datosActividad['id_ubi'];
        $Qlugar_esp = $datosActividad['lugar_esp'];
        $Qdesc_activ = $datosActividad['desc_activ'];
        $Qtipo_horario = $datosActividad['tipo_horario'];
        $Qprecio = $datosActividad['precio'];
        $Qnum_asistentes = $datosActividad['num_asistentes'];
        $Qobserv = $datosActividad['observ'];
        $Qnivel_stgr = $datosActividad['nivel_stgr'];
        $Qid_repeticion = $datosActividad['id_repeticion'];
        $Qobserv_material = $datosActividad['observ_material'];
        $Qtarifa = $datosActividad['tarifa'];
        $Qh_ini = $datosActividad['h_ini'];
        $Qh_fin = $datosActividad['h_fin'];
        $Qplazas = $datosActividad['plazas'];

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
                $error_txt .= _("No puede crear una actividad que organiza una dl/r que ya usa aquinate");
                return $error_txt;
            }

        }

        // permiso
        ////$_SESSION['oPermActividades']->setActividad($Qid_activ, $Qid_tipo_activ, $Qdl_org);
        // para dl y dlf:
        $dl_org_no_f = preg_replace('/(\.*)f$/', '\1', $Qdl_org);
        $dl_propia = (ConfigGlobal::mi_dele() == $dl_org_no_f) ? TRUE : FALSE;
        if (ConfigGlobal::is_app_installed('procesos')) {
            $_SESSION['oPermActividades']->setId_tipo_activ($Qid_tipo_activ);
            if ($_SESSION['oPermActividades']->getPermisoCrear($dl_propia) === FALSE) {
                $error_txt = _("No tiene permiso para crear una actividad de este tipo") . "<br>";
                return $error_txt;
            }
        }

//Compruebo que estén todos los campos necesarios
        if (empty($Qnom_activ) || empty($Qf_ini) || empty($Qf_fin) || empty($Qstatus) || empty($Qdl_org)) {
            $error_txt = _("debe llenar todos los campos que tengan un (*)") . "<br>";
            return $error_txt;
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
                $error_txt = _("tipo de actividad incorrecto");
                return $error_txt;
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
                $error_txt = _("hay un error, no se ha importado");
                $error_txt .= "\n" . $oActividad->getErrorTxt();
            }
        }
// Por defecto pongo todas las plazas en mi dl
        if (ConfigGlobal::is_app_installed('actividadplazas')) {
            if (!empty($Qplazas) && $Qdl_org == $mi_dele) {
                $id_activ = $oActividad->getId_activ();
                $id_dl = 0;
                $repoDelegacion = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
                $cDelegaciones = $repoDelegacion->getDelegaciones(['dl' => $mi_dele]);
                if (is_array($cDelegaciones) && count($cDelegaciones)) {
                    $id_dl = $cDelegaciones[0]->getIdDlVo()->value();
                }
                //Si es la dl_org, son plazas concedidas, sino pedidas.
                $oActividadPlazasDl = new ActividadPlazasDl(array('id_activ' => $id_activ, 'id_dl' => $id_dl, 'dl_tabla' => $mi_dele));
                $oActividadPlazasDl->DBCarregar();
                $oActividadPlazasDl->setPlazas($Qplazas);

                //print_r($oActividadPlazasDl);
                if ($oActividadPlazasDl->DBGuardar() === false) {
                    $error_txt .= _("hay un error, no se ha guardado");
                    $error_txt .= "\n" . $oActividadPlazasDl->getErrorTxt();
                }
            }
        }
        return $error_txt;
    }
}