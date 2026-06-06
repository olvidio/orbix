<?php

namespace src\actividades\application;

use src\shared\config\ConfigGlobal;
use DateInterval;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\contracts\RepeticionRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\value_objects\IdTablaCode;
use src\actividades\domain\value_objects\StatusId;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\actividadescentro\domain\entity\CentroEncargado;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullTimeLocal;
use src\shared\domain\value_objects\TimeLocal;

/**
 * Description of actividadlugar
 *
 * @author Daniel Serrabou <dani@moneders.net>
 */
class ActividadNuevoCurso
{
    public function __construct(
        private RepeticionRepositoryInterface $repeticionRepository,
        private ActividadDlRepositoryInterface $actividadDlRepository,
        private CentroEncargadoRepositoryInterface $centroEncargadoRepository,
        private ActividadProcesoTareaRepositoryInterface $actividadProcesoTareaRepository,
    ) {
    }

    /**
     *
     * registrarCambios: Para indicar a la clase Actividad que no apunte los cambios al guardar
     *
     * @var bool
     */
    private bool $registrarCambios = TRUE;
    /**
     *
     * @var bool
     */
    private bool $bVer_lista = false;
    private int $iyear_ref = 0;
    private int $iyear = 0;
    /** @var array<int, int> */
    private array $aRepeticion = [];

    /** @var list<string> */
    private array $avisosProceso = [];

    /**
     * @return array<int, int>
     */
    private function getRepetiones(): array
    {
        if ($this->aRepeticion === []) {
            $RepeticionRepository = $this->repeticionRepository;
            $cRepeticiones = $RepeticionRepository->getRepeticiones();
            foreach ($cRepeticiones as $oRepeticion) {
                $id_repeticion = $oRepeticion->getId_repeticion();
                $TipoRepeticion = $oRepeticion->getTipoRepeticion();
                if ($TipoRepeticion !== null) {
                    $this->aRepeticion[$id_repeticion] = $TipoRepeticion;
                }
            }
        }
        return $this->aRepeticion;
    }

    public function comprobar_solapes(string $inicio, string $fin): string
    {
        $txt = '';
        $ActividadDlRepository = $this->actividadDlRepository;
        $aWhere = [
            'dl_org' => ConfigGlobal::mi_delef(),
            'f_ini' => "'$inicio','$fin'",
            'status' => 4,
            '_ordre' => 'f_ini, h_ini NULLS LAST',
        ];
        $aOperador = [
            'f_ini' => 'BETWEEN',
            'status' => '<',
        ];
        $cActividades = $ActividadDlRepository->getActividades($aWhere, $aOperador);
        $num_act = count($cActividades);
        for ($i = 0; $i < ($num_act - 1); $i++) {
            $id_ubi1 = $cActividades[$i]->getId_ubi();
            if (empty($id_ubi1) || $id_ubi1 == 1)
                continue; //lugares sin determinar
            $id_ubi2 = $cActividades[$i + 1]->getId_ubi();
            if ($id_ubi1 != $id_ubi2) {
                // cambio de ubi
                continue; //salto al siguiente.
            }
            $fFin = $cActividades[$i]->getF_fin();
            $fIniNext = $cActividades[$i + 1]->getF_ini();
            if (!($fFin instanceof DateTimeLocal) || !($fIniNext instanceof DateTimeLocal)) {
                continue;
            }
            $oF_fin = clone $fFin;
            $h_fin = $this->horaComoTexto($cActividades[$i]->getH_fin(), '10:00:00');
            [$h, $m, $s] = array_pad(explode(':', $h_fin), 3, '0');
            $oF_fin->setTime((int) $h, (int) $m, (int) $s);

            $oF_ini = clone $fIniNext;
            $h_ini = $this->horaComoTexto($cActividades[$i + 1]->getH_ini(), '20:00:00');
            [$h, $m, $s] = array_pad(explode(':', $h_ini), 3, '0');
            $oF_ini->setTime((int) $h, (int) $m, (int) $s);

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

    private function horaComoTexto(TimeLocal|NullTimeLocal|null $hora, string $porDefecto): string
    {
        if ($hora === null || $hora instanceof NullTimeLocal) {
            return $porDefecto;
        }
        $txt = $hora->toDatabaseString();
        return $txt === '' ? $porDefecto : $txt;
    }

    public function borrar_actividades_periodo(string $f_ini, string $f_fin): string
    {
        $txt = '';
        $ActividadDlRepository = $this->actividadDlRepository;
        $ActividadDlRepository->deleteActividadesEnPeriodoEnProyecto($f_ini, $f_fin);

        if (ConfigGlobal::is_app_installed('procesos')) {
            // Borrar los procesos, No se puede crear una clave foránea a una tabla padre (a_actividades_all). Sólo
            // se podría con la de la dl, pero quedarían todos los procesos de las otras actividades.
            $sql = "DELETE FROM a_actividad_proceso_sv WHERE id_activ IN (
                    SELECT DISTINCT d.id_activ 
                    FROM a_actividad_proceso_sv d LEFT JOIN public.a_actividades_all a USING (id_activ)
                    WHERE a.id_activ IS NULL
                 )";
            if (!$ActividadDlRepository->execMaintenanceSql($sql)) {
                $txt .= _("error al borrar los procesos de la sv") . "<br>";
            }
            $sql = "DELETE FROM a_actividad_proceso_sf WHERE id_activ IN (
                    SELECT DISTINCT d.id_activ 
                    FROM a_actividad_proceso_sf d LEFT JOIN public.a_actividades_all a USING (id_activ)
                    WHERE a.id_activ IS NULL
                 )";
            if (!$ActividadDlRepository->execMaintenanceSql($sql)) {
                $txt .= _("error al borrar los procesos de la sf") . "<br>";
            }
        }

        // comprobar que no quedan actividades en otro estado
        $cActividades = $ActividadDlRepository->getArrayActividadesEnPeriodoNoEnProyecto($f_ini, $f_fin);
        $rta_txt = '';
        foreach ($cActividades as $nom_activ) {
            $rta_txt .= (string) $nom_activ . '<br>';
        }
        if (!empty($rta_txt)) {
            $txt .= _("actividades no eliminadas, porque su estado no es proyecto") . ":<br>";
            $txt .= $rta_txt;
        }
        return $txt;
    }

    public function crear_actividad(ActividadAll $oActividadOrigen): string
    {
        $txt = '';
        $aRepeticion = $this->getRepetiones();

        $id_repeticion = $oActividadOrigen->getId_repeticion();
        if (empty($id_repeticion)) {
            $txt .= sprintf(_("error (no tiene definida la repetición) en la actividad: %s"), $oActividadOrigen->getNom_activ());
            $txt .= "<br>";
            return $txt;
        }
        $tipo = $aRepeticion[$id_repeticion] ?? 0;
        $oFini = $oActividadOrigen->getF_ini();
        $oFfin = $oActividadOrigen->getF_fin();
        if (!($oFini instanceof DateTimeLocal) || !($oFfin instanceof DateTimeLocal)) {
            return $txt . _('fechas de actividad no válidas') . '<br>';
        }
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
            default:
                return $txt;
        }
        //cambio el nombre
        $f_ini_new = $oFini->getFromLocal();
        $f_fin_new = $oFfin->getFromLocal();
        $fechas_new = "$f_ini_new-$f_fin_new";
        $nom_activ = $oActividadOrigen->getNom_activ();

        $patron = '/^(.*)(\(.*?-.*?\))(.*)/';
        $sustitucion = '$1(' . $fechas_new . ')$3';
        $nom_activ_new = preg_replace($patron, $sustitucion, $nom_activ);
        if (!is_string($nom_activ_new) || $nom_activ_new === '') {
            $nom_activ_new = $nom_activ;
        }

        if ($this->getVer_lista()) {
            echo "$tipo=> $fechas_new :: $nom_activ_new<br>";
        }
        //cambio el status a proyecto:
        $status = StatusId::PROYECTO;
        $ActividadDlRepository = $this->actividadDlRepository;
        $newId = $ActividadDlRepository->getNewId();
        $newIdActividad = $ActividadDlRepository->getNewIdActividad($newId);
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
        $hIni = $oActividadOrigen->getH_ini();
        $hFin = $oActividadOrigen->getH_fin();
        $oActividad->setH_ini($hIni instanceof TimeLocal ? $hIni : null);
        $oActividad->setH_fin($hFin instanceof TimeLocal ? $hFin : null);
        if ($ActividadDlRepository->Guardar($oActividad, $this->registrarCambios) === false) {
            echo "ERROR: no se ha guardado la actividad<br>";
            exit;
        }

        // cojo el valor del último insert 
        $id_actividad_new = $oActividad->getId_activ();

        if (ConfigGlobal::is_app_installed('actividadescentro')) {
            // También copio los centros encargados.
            $CentroEncargadoRepository = $this->centroEncargadoRepository;
            $cEncargados = $CentroEncargadoRepository->getCentrosEncargados(array('id_activ' => $oActividadOrigen->getId_activ()));
            foreach ($cEncargados as $oCentroEncargado) {
                if (!($oCentroEncargado instanceof CentroEncargado)) {
                    continue;
                }
                $newEncargado = clone $oCentroEncargado;
                $newEncargado->setId_activ($id_actividad_new);
                if ($CentroEncargadoRepository->Guardar($newEncargado, $this->registrarCambios) === false) {
                    echo _("hay un error, no se ha guardado");
                    echo "\n" . $CentroEncargadoRepository->getErrorTxt();
                }
            }
        }
        if (ConfigGlobal::is_app_installed('procesos')) {
            // También creo las fases-tareas
            $this->crear_fases($id_actividad_new, $oActividad);
        }
        return $txt;
    }

    private function crear_fases(int $id_activ, ActividadAll $oActividad): void
    {
        $ActividadProcesoTareaRepository = $this->actividadProcesoTareaRepository;
        $ActividadProcesoTareaRepository->generarProceso((string) $id_activ, '', false, $oActividad);
        foreach ($ActividadProcesoTareaRepository->consumirAvisosGenerarProceso() as $aviso) {
            $this->avisosProceso[] = $aviso;
        }
    }

    /**
     * @return list<string>
     */
    public function consumirAvisosProceso(): array
    {
        $avisos = $this->avisosProceso;
        $this->avisosProceso = [];
        return $avisos;
    }


    /**
     * Para indicar a la clase Actividad que apunte los cambios al guardar
     */
    public function isRegistrarCambios(): bool
    {
        return $this->registrarCambios;
    }

    public function setRegistrarCambios(bool $registrarCambios = TRUE): self
    {
        $this->registrarCambios = $registrarCambios;
        return $this;
    }

    /**
     * bVer_lista
     * @return bool
     */
    public function getVer_lista(): bool
    {
        return $this->bVer_lista;
    }

    /**
     * bVer_lista
     * @param bool $bVer_lista
     * @return ActividadNuevoCurso
     */
    public function setVer_lista(bool $bVer_lista): self
    {
        $this->bVer_lista = $bVer_lista;
        return $this;
    }

    /**
     * iyear
     * @return integer
     */
    public function getYear(): int
    {
        return $this->iyear;
    }

    /**
     * iyear
     * @param int $iyear
     * @return ActividadNuevoCurso
     */
    public function setYear(int $iyear): self
    {
        $this->iyear = $iyear;
        return $this;
    }

    /**
     * iyear_ref
     * @return integer
     */
    public function getYear_ref(): int
    {
        return $this->iyear_ref;
    }

    /**
     * iyear_ref
     * @param int $iyear_ref
     * @return ActividadNuevoCurso
     */
    public function setYear_ref(int $iyear_ref): self
    {
        $this->iyear_ref = $iyear_ref;
        return $this;
    }
}