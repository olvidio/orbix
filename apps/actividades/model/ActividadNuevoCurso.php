<?php

namespace actividades\model;

use core\ConfigGlobal;
use DateInterval;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\contracts\RepeticionRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\value_objects\IdTablaCode;
use src\actividades\domain\value_objects\StatusId;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Description of actividadlugar
 *
 * @author Daniel Serrabou <dani@moneders.net>
 */
class ActividadNuevoCurso
{

    /**
     *
     * bQuiet: Para indicar a la clase Actividad que no apunte los cambios al guardar
     *
     * @var bool
     */
    private $bQuiet = FALSE;
    /**
     *
     * @var bool
     */
    private $bVer_lista = FALSE;
    /**
     *
     * @var integer
     */
    private $iyear_ref;
    /**
     *
     * @var integer
     */
    private $iyear;
    /**
     *
     * @var array
     */
    private $aRepeticion;

    private function getRepetiones()
    {
        if (!isset($this->aRepeticion)) {
            $RepeticionRepository = $GLOBALS['container']->get(RepeticionRepositoryInterface::class);
            $cRepeticiones = $RepeticionRepository->getRepeticiones();
            $this->aRepeticion = [];
            foreach ($cRepeticiones as $oRepeticion) {
                $id_repeticion = $oRepeticion->getId_repeticion();
                $TipoRepeticion = $oRepeticion->getTipo();
                $this->aRepeticion[$id_repeticion] = $TipoRepeticion;
            }
        }
        return $this->aRepeticion;
    }

    /**
     * Busca el solape de actividades en el periodo
     *
     * @param string date iso $inicio
     * @param string date iso $fin
     */
    public function comprobar_solapes($inicio, $fin)
    {
        $txt = '';
        $ActividadDlRepository = $GLOBALS['container']->get(ActividadDlRepositoryInterface::class);
        $sQry = "SELECT id_activ, to_char(f_ini,'YYYYMMDD')||COALESCE(to_char(h_ini,'HH24MISS'),'200000') as inicio 
                    FROM a_actividades_dl
                    WHERE dl_org = '" . ConfigGlobal::mi_delef() . "' AND f_ini >= '$inicio' AND f_ini <= '$fin' AND status < 4 
                    ORDER BY inicio";
        $cActividades = $ActividadDlRepository->getActividadesQuery($sQry);
        $num_act = count($cActividades);
        for ($i = 0; $i < ($num_act - 1); $i++) {
            $id_ubi1 = $cActividades[$i]->getId_ubi();
            if (empty($id_ubi1) || $id_ubi1 == 1) continue; //lugares sin determinar
            $id_ubi2 = $cActividades[$i + 1]->getId_ubi();
            if ($id_ubi1 != $id_ubi2) {
                // cambio de ubi
                continue; //salto al siguiente.
            }
            $oF_fin = $cActividades[$i]->getF_fin();
            $h_fin = $cActividades[$i]->getH_fin();
            if (empty($h_fin)) $h_fin = '10:00:00';
            list($h, $m, $s) = explode(':', $h_fin);
            $oF_fin->setTime($h, $m, $s);

            $oF_ini = $cActividades[$i + 1]->getF_ini();
            $h_ini = $cActividades[$i + 1]->getH_ini();
            if (empty($h_ini)) $h_ini = '20:00:00';
            list($h, $m, $s) = explode(':', $h_ini);
            $oF_ini->setTime($h, $m, $s);

            $dif = $oF_fin->diff($oF_ini);
            //echo $dif->format('%R%a %H');
            if ($dif->format('%R') === '-') {
                //echo $dif->format('%R%a %H');
                $txt .= _("hay un solape entre") . ':  ';
                $txt .= $cActividades[$i]->getNom_activ();
                $txt .= '  ' . _("y") . '  ';
                $txt .= $cActividades[$i + 1]->getNom_activ();
                $txt .= "<br>";
            }
        }
        return $txt;
    }

    /**
     * borra las actividades en proyecto para las fechas indicadas, y
     * devuelve una lista con las actividades que no están en proyecto, y que no se han borrado.
     *
     * @param string date iso $f_ini
     * @param string date iso $f_fin
     */
    function borrar_actividades_periodo($f_ini, $f_fin)
    {
        $txt = '';
        $ActividadDlRepository = $GLOBALS['container']->get(ActividadDlRepositoryInterface::class);
        $ActividadDlRepository->deleteActividadesPeriodo($f_ini, $f_fin);

        if (ConfigGlobal::is_app_installed('procesos')) {
            // Borrar los procesos, No se puede crear una clave foránea a una tabla padre (a_actividades_all). Sólo
            // se podría con la de la dl, pero quedarían todos los procesos de las otras actividades.
            $sql = "DELETE FROM a_actividad_proceso_sv WHERE id_activ IN (
                    SELECT DISTINCT d.id_activ 
                    FROM a_actividad_proceso_sv d LEFT JOIN public.a_actividades_all a USING (id_activ)
                    WHERE a.id_activ IS NULL
                 )";
            if ($ActividadDlRepository->getActividadesQuery($sql) === false) {
                $txt .= _("error al borrar los procesos de la sv") . "<br>";
            }
            $sql = "DELETE FROM a_actividad_proceso_sf WHERE id_activ IN (
                    SELECT DISTINCT d.id_activ 
                    FROM a_actividad_proceso_sf d LEFT JOIN public.a_actividades_all a USING (id_activ)
                    WHERE a.id_activ IS NULL
                 )";
            if ($ActividadDlRepository->getActividadesQuery($sql) === false) {
                $txt .= _("error al borrar los procesos de la sf") . "<br>";
            }
        }

        // comprobar que no quedan actividades en otro estado
        $cActividades = $ActividadDlRepository->getArrayActividadesEnPeriodoNoEnProyecto($f_ini,$f_fin);
        $rta_txt = '';
        foreach ($cActividades as $oActividad) {
            $rta_txt .= $oActividad->getNom_activ() . "<br>";
        }
        if (!empty($rta_txt)) {
            $txt .= _("actividades no eliminadas, porque su estado no es proyecto") . ":<br>";
            $txt .= $rta_txt;
        }
        return $txt;
    }

    function crear_actividad($oActividadOrigen)
    {
        $txt = '';
        $aRepeticion = $this->getRepetiones();

        $id_repeticion = $oActividadOrigen->getId_repeticion();
        if (empty($id_repeticion)) {
            $txt .= sprintf(_("error (no tiene definida la repetición) en la actividad: %s"), $oActividadOrigen->getNom_activ());
            $txt .= "<br>";
            return $txt;
        }
        $tipo = $aRepeticion[$id_repeticion];
        $oFini = $oActividadOrigen->getF_ini();
        $oFfin = $oActividadOrigen->getF_fin();
        switch ($tipo) {
            case 1: // por dia de la semana
                // miro si es bisiesto o si el anterior es bisiesto
                /* Con la última versión del php, el mktime ya se aclara con los bisiestos */
                $inc_year = $this->iyear - $this->iyear_ref;
                $num_dias = $inc_year * 364;
                $periodo = "P" . $num_dias . "D";
                $oFini->add(new DateInterval($periodo));
                $oFfin->add(new DateInterval($periodo));
                break;
            case 2: // por dia del año
                $inc_year = $this->iyear - $this->iyear_ref;
                $periodo = "P" . $inc_year . "Y";
                $oFini->add(new DateInterval($periodo));
                $oFfin->add(new DateInterval($periodo));
                break;
            case 3: // por dia de domingo de pascua
                $oDomingo_pascua = new DateTimeLocal();
                $oDomingo_pascua_new = new DateTimeLocal();
                $oDomingo_pascua->setTimestamp(easter_date($this->iyear_ref));
                $oDomingo_pascua_new->setTimestamp(easter_date($this->iyear));
                $dif_pascua = $oDomingo_pascua->diff($oDomingo_pascua_new);
                $oFini->add($dif_pascua);
                $oFfin->add($dif_pascua);
                break;
            default: // El resto no se repite.
                return;
        }
        //cambio el nombre
        $f_ini_new = $oFini->getFromLocal();
        $f_fin_new = $oFfin->getFromLocal();
        $fechas_new = "$f_ini_new-$f_fin_new";
        $nom_activ = $oActividadOrigen->getNom_activ();

        $patron = '/^(.*)(\(.*?-.*?\))(.*)/';
        $sustitucion = '$1(' . $fechas_new . ')$3';
        $nom_activ_new = preg_replace($patron, $sustitucion, $nom_activ);

        if ($this->Ver_lista) {
            echo "$tipo=> $fechas_new :: $nom_activ_new<br>";
        }
        //cambio el status a proyecto:
        $status = StatusId::PROYECTO;
        $ActividadDlRepository = $GLOBALS['container']->get(ActividadDlRepositoryInterface::class);
        $newId = $ActividadDlRepository->newId();
        $newIdActividad = $ActividadDlRepository->newIdActividad($newId);
        $oActividad = new ActividadAll();
        $oActividad->setId_auto($newId);
        $oActividad->setId_activ($newIdActividad);
        $oActividad->setIdTablaVo(new IdTablaCode('dl'));

        $oActividad->setId_tipo_activ($oActividadOrigen->getId_tipo_activ());
        $oActividad->setDl_org($oActividadOrigen->getDl_org());
        $oActividad->setNom_activ($nom_activ_new);
        $oActividad->setId_ubi($oActividadOrigen->getId_ubi());
        $oActividad->setDesc_activ($oActividadOrigen->getDesc_activ());
        $oActividad->setF_ini($oFini);
        $oActividad->setF_fin($oFfin);
        //$oActividad->setTipo_horario($oActividadOrigen->getTipo_horario());
        $oActividad->setPrecio($oActividadOrigen->getPrecio());
        $oActividad->setNum_asistentes($oActividadOrigen->getNum_asistentes());
        $oActividad->setStatus($status);
        $oActividad->setObserv($oActividadOrigen->getObserv());
        $oActividad->setNivel_stgr($oActividadOrigen->getNivel_stgr());
        $oActividad->setId_repeticion($oActividadOrigen->getId_repeticion());
        $oActividad->setObserv_material($oActividadOrigen->getObserv_material());
        $oActividad->setLugar_esp($oActividadOrigen->getLugar_esp());
        $oActividad->setTarifa($oActividadOrigen->getTarifa());
        $oActividad->setH_ini($oActividadOrigen->getH_ini());
        $oActividad->setH_fin($oActividadOrigen->getH_fin());
        // TODO: se le pasa el valor Quiet, para que no apunte los cambios.
        //if ($ActividadDlRepository->Guardar($oActividad, $this->getQuiet()) === false) {
        if ($ActividadDlRepository->Guardar($oActividad) === false) {
            echo "ERROR: no se ha guardado la actividad<br>";
            exit;
        }

        // cojo el valor del último insert 
        $id_actividad_new = $oActividad->getId_activ();

        if (ConfigGlobal::is_app_installed('actividadescentro')) {
            // También copio los centros encargados.
            $CentroEncargadoRepository = $GLOBALS['container']->get(CentroEncargadoRepositoryInterface::class);
            $cEncargados = $CentroEncargadoRepository->getCentrosEncargados(array('id_activ' => $oActividadOrigen->getId_activ()));
            foreach ($cEncargados as $oCentroEncargado) {
                $newEncargado = clone $oCentroEncargado;
                $newEncargado->setId_activ($id_actividad_new);
                if ($CentroEncargadoRepository->Guardar($newEncargado) === false) {
                    echo _("hay un error, no se ha guardado");
                    echo "\n" . $newEncargado->getErrorTxt();
                }
            }
        }
        if (ConfigGlobal::is_app_installed('procesos')) {
            // También creo las fases-tareas
            $this->crear_fases($id_actividad_new);
        }
    }

    private function crear_fases($id_activ)
    {
        //echo "generando fases de $id_activ,$id_tipo_activ...<br>";
        $ActividadProcesoTareaRepository = $GLOBALS['container']->get(ActividadProcesoTareaRepositoryInterface::class);
        $ActividadProcesoTareaRepository->generarProceso($id_activ);
    }


    /**
     * bQuiet Para indicar a la clase Actividad que no apunte los cambios al guardar
     * @return bool
     */
    public function getQuiet()
    {
        return $this->bQuiet;
    }

    /**
     * bQuiet
     * @param bool $bQuiet
     * @return ActividadNuevoCurso
     */
    public function setQuiet(bool $bQuiet)
    {
        $this->bQuiet = $bQuiet;
        return $this;
    }

    /**
     * bVer_lista
     * @return bool
     */
    public function getVer_lista()
    {
        return $this->bVer_lista;
    }

    /**
     * bVer_lista
     * @param bool $bVer_lista
     * @return ActividadNuevoCurso
     */
    public function setVer_lista(bool $bVer_lista)
    {
        $this->bVer_lista = $bVer_lista;
        return $this;
    }

    /**
     * iyear
     * @return integer
     */
    public function getYear()
    {
        return $this->iyear;
    }

    /**
     * iyear
     * @param int $iyear
     * @return ActividadNuevoCurso
     */
    public function setYear(int $iyear)
    {
        $this->iyear = $iyear;
        return $this;
    }

    /**
     * iyear_ref
     * @return integer
     */
    public function getYear_ref()
    {
        return $this->iyear_ref;
    }

    /**
     * iyear_ref
     * @param int $iyear_ref
     * @return ActividadNuevoCurso
     */
    public function setYear_ref(int $iyear_ref)
    {
        $this->iyear_ref = $iyear_ref;
        return $this;
    }
}
