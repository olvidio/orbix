<?php

namespace actividades\domain;

use core\ConfigGlobal;
use core\DBPropiedades;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\contracts\ActividadExRepositoryInterface;
use src\actividades\domain\contracts\ImportadaRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\entity\Importada;
use src\actividades\domain\value_objects\IdTablaCode;
use src\actividades\domain\value_objects\NivelStgrId;
use src\actividades\domain\value_objects\StatusId;
use src\actividadplazas\domain\contracts\ActividadPlazasDlRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use ubis;
use web\DateTimeLocal;
use web\TimeLocal;

class ActividadNueva
{

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
        //$Qtipo_horario = $datosActividad['tipo_horario'];
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
        if ($Qdl_org !== ConfigGlobal::mi_delef()) {
            $Qpublicado = true;
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
                throw new \RuntimeException(_("No puede crear una actividad que organiza una dl/r que ya usa aquinate"));
            }

        }

        // permiso
        // para dl y dlf:
        $dl_org_no_f = preg_replace('/(\.*)f$/', '\1', $Qdl_org);
        $dl_propia = ConfigGlobal::mi_dele() === $dl_org_no_f;
        if (ConfigGlobal::is_app_installed('procesos')) {
            $_SESSION['oPermActividades']->setId_tipo_activ($Qid_tipo_activ);
            if ($_SESSION['oPermActividades']->getPermisoCrear($dl_propia) === FALSE) {
                throw new \RuntimeException(_("No tiene permiso para crear una actividad de este tipo"));
            }
        }

        //Compruebo que estén todos los campos necesarios
        if (empty($Qnom_activ) || empty($Qf_ini) || empty($Qf_fin) || empty($Qstatus) || empty($Qdl_org)) {
            throw new \RuntimeException(_("debe llenar todos los campos que tengan un (*)"));
        }

        $isfsv = substr($Qid_tipo_activ, 0, 1);
        $mi_dele = ConfigGlobal::mi_delef($isfsv);
        if ($Qdl_org === $mi_dele) {
            $ActividadRepository = $GLOBALS['container']->get(ActividadDlRepositoryInterface::class);
            $newId = $ActividadRepository->newId();
            $newIdActividad = $ActividadRepository->newIdActividad($newId);
            $oActividad = new ActividadAll();
            $oActividad->setId_activ($newIdActividad);
            $oActividad->setIdTablaVo(new IdTablaCode('dl'));
        } else {
            $ActividadRepository = $GLOBALS['container']->get(ActividadExRepositoryInterface::class);
            $newId = $ActividadRepository->newId();
            $newIdActividad = $ActividadRepository->newIdActividad($newId);
            $oActividad = new ActividadAll();
            $oActividad->setId_activ($newIdActividad);
            $oActividad->setPublicado('t');
            $oActividad->setIdTablaVo(new IdTablaCode('ex'));
            $Qstatus = StatusId::ACTUAL; // Que sea estado actual.
        }
        $oActividad->setDl_org($Qdl_org);
        if (isset($Qid_tipo_activ)) {
            if ($oActividad->setId_tipo_activ($Qid_tipo_activ) === false) {
                throw new \RuntimeException(_("tipo de actividad incorrecto"));
            }
        }
        $oActividad->setNom_activ($Qnom_activ);

        // En el caso de tener id_ubi (!=1) borro el campo lugar_esp.
        if (!empty($Qid_ubi) && $Qid_ubi !== 1) {
            $oActividad->setId_ubi($Qid_ubi);
            $oActividad->setLugar_esp('');
        } else {
            $oActividad->setId_ubi($Qid_ubi);
            $oActividad->setLugar_esp($Qlugar_esp);
        }
        $oActividad->setDesc_activ($Qdesc_activ);
        // asegurar tipo correcto para f_ini
        $oF_ini = empty($Qf_ini) ? null : DateTimeLocal::createFromLocal($Qf_ini);
        $oActividad->setF_ini($oF_ini);
        // asegurar tipo correcto para f_fin
        $oF_fin = empty($Qf_fin) ? null : DateTimeLocal::createFromLocal($Qf_fin);
        $oActividad->setF_fin($oF_fin);
        //$oActividad->setTipo_horario($Qtipo_horario);
        $oActividad->setPrecio($Qprecio);
        $oActividad->setNum_asistentes($Qnum_asistentes);
        $oActividad->setStatus($Qstatus);
        $oActividad->setObserv($Qobserv);
        // Si nivel_stgr está vacío, pongo el calculado.
        if (empty($Qnivel_stgr)) {
            $Qnivel_stgr = NivelStgrId::generarNivelStgr($Qid_tipo_activ);
        }
        $oActividad->setNivel_stgr($Qnivel_stgr);
        $oActividad->setId_repeticion($Qid_repeticion);
        $oActividad->setObserv_material($Qobserv_material);
        $oActividad->setTarifa($Qtarifa);
        // asegurar tipo correcto para h_ini
        $oH_ini = empty($Qh_ini) ? null : TimeLocal::fromString($Qh_ini);
        $oActividad->setH_ini($oH_ini);
        // asegurar tipo correcto para h_fin
        $oH_fin = empty($Qh_fin) ? null : TimeLocal::fromString($Qh_fin);
        $oActividad->setH_fin($oH_fin);
        $oActividad->setPublicado($Qpublicado);
        $oActividad->setPlazas($Qplazas);
        if ($ActividadRepository->Guardar($oActividad) === false) {
            throw new \RuntimeException(_("hay un error, no se ha guardado") . ": " . $ActividadRepository->getErrorTxt());
        }
        // si estoy creando una actividad de otra dl es porque la quiero importar.
        if ($Qdl_org !== $mi_dele) {
            $ImportadaRepository = $GLOBALS['container']->get(ImportadaRepositoryInterface::class);
            $id_activ = $oActividad->getId_activ();
            $oImportada = new Importada();
            $oImportada->setId_activ($id_activ);
            if ($ImportadaRepository->Guardar($oImportada) === false) {
                throw new \RuntimeException(_("hay un error, no se ha importado") . ": " . $ImportadaRepository->getErrorTxt());
            }
        }
        // Por defecto pongo todas las plazas en mi dl
        if (ConfigGlobal::is_app_installed('actividadplazas')) {
            if (!empty($Qplazas) && $Qdl_org === $mi_dele) {
                $id_activ = $oActividad->getId_activ();
                $id_dl = 0;
                $repoDelegacion = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
                $cDelegaciones = $repoDelegacion->getDelegaciones(['dl' => $mi_dele]);
                if (is_array($cDelegaciones) && count($cDelegaciones)) {
                    $id_dl = $cDelegaciones[0]->getIdDlVo()->value();
                }
                //Si es la dl_org, son plazas concedidas, sino pedidas.
                $ActividadPlazasDlRepository = $GLOBALS['container']->get(ActividadPlazasDlRepositoryInterface::class);
                $oActividadPlazasDl = $ActividadPlazasDlRepository->getActividadesPlazas(['id_activ' => $id_activ, 'id_dl' => $id_dl, 'dl_tabla' => $mi_dele]);
                $oActividadPlazasDl->setPlazas($Qplazas);

                //print_r($oActividadPlazasDl);
                if ($ActividadPlazasDlRepository->Guardar($oActividadPlazasDl) === false) {
                    throw new \RuntimeException(_("hay un error, no se ha guardado") . ": " . $ActividadPlazasDlRepository->getErrorTxt());
                }
            }
        }
        return $id_activ;
    }
}