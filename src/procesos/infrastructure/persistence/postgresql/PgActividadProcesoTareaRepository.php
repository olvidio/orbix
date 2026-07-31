<?php

namespace src\procesos\infrastructure\persistence\postgresql;

use src\shared\infrastructure\logging\GestorErrores;

use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\postgresql\Set;
use PDO;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\contracts\ActividadExRepositoryInterface;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\value_objects\StatusId;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\contracts\ProcesoTipoRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use src\procesos\domain\entity\ActividadProcesoTarea;
use src\procesos\domain\value_objects\FaseId;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\traits\HandlesPdoErrors;
use src\ubis\domain\contracts\CasaRepositoryInterface;

/**
 * Clase que adapta la tabla a_actividad_proceso_sv a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 26/12/2025
 */
class PgActividadProcesoTareaRepository extends ClaseRepository implements ActividadProcesoTareaRepositoryInterface
{
    use HandlesPdoErrors;

    /** @var list<string> */
    private array $avisosGenerarProceso = [];

    public function __construct(
        private readonly TipoDeActividadRepositoryInterface $tipoDeActividadRepository,
        private readonly CasaRepositoryInterface $casaRepository,
        private readonly ActividadAllRepositoryInterface $actividadAllRepository,
        private readonly ActividadDlRepositoryInterface $actividadDlRepository,
        private readonly ActividadExRepositoryInterface $actividadExRepository,
        private readonly TareaProcesoRepositoryInterface $tareaProcesoRepository,
        private readonly ProcesoTipoRepositoryInterface $procesoTipoRepository,
    ) {
        $oDbl = GlobalPdo::get('oDBC');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBC_Select');
        $this->setoDbl_select($oDbl_Select);
         if (ConfigGlobal::mi_sfsv() === 1) {
            $this->setNomTabla('a_actividad_proceso_sv');
        } else {
            $this->setNomTabla('a_actividad_proceso_sf');
        }
    }

    public function borrarFaseTareaInexistente(int $id_tipo_proceso, int $id_fase, int $id_tarea): void
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $nom_tabla_procesos = 'a_tareas_proceso';

        $temp_table = "tmp_borrar_" . $id_tipo_proceso . "_" . $id_fase . "_" . $id_tarea;
        $this->pdoQuery($oDbl, "DROP TABLE IF EXISTS $temp_table", __METHOD__, __FILE__, __LINE__);
        $sQuery = "CREATE TEMPORARY TABLE $temp_table AS ";
        $sQuery .= "SELECT a.id_activ,a.id_fase,id_tarea
                    FROM $nom_tabla a LEFT JOIN $nom_tabla_procesos p USING (id_tipo_proceso,id_fase,id_tarea)
                    WHERE id_tipo_proceso=$id_tipo_proceso AND p.id_fase IS NULL";
        $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        // Borrar:
        $sQry_INSERT = "DELETE FROM $nom_tabla a
                        USING $temp_table t
                        WHERE  a.id_activ = t.id_activ
                            AND a.id_fase = t.id_fase
                            AND a.id_tarea = t.id_tarea
                       ";
        $this->pdoQuery($oDbl, $sQry_INSERT, __METHOD__, __FILE__, __LINE__);
    }

    public function addFaseTarea(int $id_tipo_proceso, int $id_fase, int $id_tarea): void
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $temp_table = "tmp_proceso_" . $id_fase . "_" . $id_tarea;
        $this->pdoQuery($oDbl, "DROP TABLE IF EXISTS $temp_table", __METHOD__, __FILE__, __LINE__);
        $sQuery = "CREATE TEMPORARY TABLE $temp_table AS ";
        $sQuery .= "(SELECT DISTINCT id_activ FROM $nom_tabla WHERE id_tipo_proceso=$id_tipo_proceso)";
        $sQuery .= " EXCEPT ";
        $sQuery .= "(SELECT DISTINCT id_activ FROM $nom_tabla 
                    WHERE id_tipo_proceso=$id_tipo_proceso AND id_fase=$id_fase AND id_tarea=$id_tarea)";
        $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        // Añadir fase:
        $sQry_INSERT = "INSERT INTO $nom_tabla (id_tipo_proceso,id_activ,id_fase,id_tarea)    
                        SELECT $id_tipo_proceso, id_activ, $id_fase, $id_tarea FROM $temp_table";
        $this->pdoQuery($oDbl, $sQry_INSERT, __METHOD__, __FILE__, __LINE__);
    }
    /**
     * @return array<string, bool>
     */
    public function getListaFaseEstado(int $iid_activ): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT * FROM $nom_tabla WHERE id_activ=$iid_activ
                ";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $aFasesEstado = [];
        foreach ($stmt as $aDades) {
            if (!is_array($aDades)) {
                continue;
            }
            $id_fase = isset($aDades['id_fase']) && is_numeric($aDades['id_fase']) ? (int) $aDades['id_fase'] : 0;
            $id_tarea = isset($aDades['id_tarea']) && is_numeric($aDades['id_tarea']) ? (int) $aDades['id_tarea'] : 0;
            $f = $id_fase . '#' . $id_tarea;
            $aFasesEstado[$f] = \src\shared\domain\helpers\FuncTablasSupport::isTrue($aDades['completado'] ?? false) ?? false;
        }
        return $aFasesEstado;
    }

    /**
     * Devuelve el estado de la fase ("ok atn sacd" que es la 5)
     * o FALSE si falla.
     *     * @param int $iid_activ
     */
    public function getSacdAprobado(int $iid_activ): ?bool
    {
        $oDbl = $this->getoDbl_Select();
        $nomTablaOriginal = $this->getNomTabla();
        try {
            // Mirar el proceso de la sv
            $this->setNomTabla('a_actividad_proceso_sv');
            // La fase ok sacd es la 5. Por definición
            $id_fase_atn_sacd = FaseId::FASE_OK_SACD;
            $nom_tabla = $this->getNomTabla();

            $sQry = "SELECT completado FROM $nom_tabla WHERE id_activ=" . $iid_activ . " AND id_fase=$id_fase_atn_sacd ";
            $stmt = $this->pdoQuery($oDbl, $sQry, __METHOD__, __FILE__, __LINE__);
            if ($stmt === false) {
                return null;
            }
            if ($stmt->rowCount() === 1) {
                $aDades = $stmt->fetch(\PDO::FETCH_ASSOC);
                if (!is_array($aDades)) {
                    return null;
                }
                return \src\shared\domain\helpers\FuncTablasSupport::isTrue($aDades['completado']);
            }
            return null;
        } finally {
            $this->setNomTabla($nomTablaOriginal);
        }
    }

    /**
     * En general genera los dos procesos, para sv y sf.
     * Si se le pasa el parámetro isfsv, sólo genera el proceso correspondiente.
     *
     * @param string $iid_activ
     * @param int|null $isfsv
     * @param boolean $force para forzar a borrar el proceso y generarlo de nuevo
     * @return boolean|int id_fase.
     */
    public function generarProceso(
        string $iid_activ = '',
        ?int $isfsv = null,
        bool $force = FALSE,
        ?ActividadAll $oActividad = null,
    ): bool|int {
        $id_activ = (int) $iid_activ;
        if ($oActividad === null) {
            $oActividad = $this->findActividadForProceso($id_activ);
        }
        if ($oActividad === null) {
            $this->registrarAvisoGenerarProceso(sprintf(_("La actividad: %s ya no existe"), $iid_activ));
            return TRUE;
        }
        $iid_tipo_activ = $oActividad->getIdTipoActivVo()->value();
        $oTipoDeActividad = $this->tipoDeActividadRepository->findById($iid_tipo_activ);

        if (empty($oTipoDeActividad)) {
            $this->registrarAvisoGenerarProceso(sprintf(_("No existe este tipo de actividad: %s"), $iid_tipo_activ));
            return TRUE;
        }
        $dl_org = $oActividad->getDl_org() ?? '';
        $dl_org_no_f = preg_replace('/(\.*)f$/', '\1', $dl_org);

        if (empty($isfsv)) {
            $a_sfsv = [1, 2];
            $isfsv = ConfigGlobal::mi_sfsv();
        } else {
            $a_sfsv = [$isfsv];
        }
        $iid_fase = [];
        foreach ($a_sfsv as $sfsv) {
            if ($sfsv === 1) {
                $this->setNomTabla('a_actividad_proceso_sv');
            } else {
                $this->setNomTabla('a_actividad_proceso_sf');
            }
            if ($dl_org_no_f === ConfigGlobal::mi_dele()) {
                $id_tipo_proceso = $oTipoDeActividad->getId_tipo_proceso((int) $sfsv);
            } else {
                // NO se genera si:
                // - es una actividad de otra dl,
                // - y de la otra sección
                // - y no se hace en una casa de la dl.
                if ($isfsv != $sfsv) {
                    $id_ubi = $oActividad->getId_ubi();
                    $oCasa = $this->casaRepository->findById($id_ubi ?? 0);
                    $dl_casa = $oCasa?->getDlVo()?->value();
                    if ($dl_casa != ConfigGlobal::mi_dele()) {
                        continue;
                    }
                }
                $id_tipo_proceso = $oTipoDeActividad->getId_tipo_proceso_ex((int) $sfsv);
            }
            if (empty($id_tipo_proceso)) {
                $this->registrarAvisoGenerarProceso(
                    sprintf(_("No tiene definido el proceso para este tipo de actividad: %s de sv/sf: %s"), $iid_tipo_activ, $sfsv)
                );
                return TRUE;
            }
            // Asegurar que no existe, a veces al hacerlo para las dos secciones, una lo tiene y otra no:
            // >> Cuando se hace manual, es porque se quiere regenerar y hay que forzar:
            if ($force === FALSE) {
                $cActividadProcesoTarea = $this->getActividadProcesoTareas(['id_activ' => $iid_activ]);
                if (empty($cActividadProcesoTarea)) {
                    $iid_fase[$sfsv] = $this->generar($id_activ, $id_tipo_proceso, $sfsv, $force, $oActividad);
                } else {
                    $iid_fase[$sfsv] = $cActividadProcesoTarea[0]->getIdFaseVo()?->value() ?? 0;
                }
            } else {
                $iid_fase[$sfsv] = $this->generar($id_activ, $id_tipo_proceso, $sfsv, $force, $oActividad);
            }
        }

        // devuelve la fase del proceso propio
        return $iid_fase[$isfsv];
    }

    public function consumirAvisosGenerarProceso(): array
    {
        $avisos = $this->avisosGenerarProceso;
        $this->avisosGenerarProceso = [];
        return $avisos;
    }

    private function registrarAvisoGenerarProceso(string $mensaje): void
    {
        $this->avisosGenerarProceso[] = $mensaje;
    }

    /**
     * Busca la actividad en all, dl o ex (p. ej. recién creada en dl y aún no visible en all).
     */
    private function findActividadForProceso(int $id_activ): ?ActividadAll
    {
        foreach (
            [
                $this->actividadAllRepository,
                $this->actividadDlRepository,
                $this->actividadExRepository,
            ] as $repository
        ) {
            $oActividad = $repository->findById($id_activ);
            if ($oActividad !== null) {
                return $oActividad;
            }
        }
        return null;
    }

    /**
     * @return list<int>
     */
    public function getFasesCompletadas(int $iid_activ): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        // No puedo hacer la consulta con WHERE completado='t',
        // porque hay que distinguirlo de si existe el proceso o no, y hay que crearlo.
        //$sQuery = "SELECT * FROM $nom_tabla WHERE id_activ=$iid_activ
        //        AND completado='t' ";
        $sQuery = "SELECT * FROM $nom_tabla WHERE id_activ=$iid_activ ";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $aFasesCompletadas = [];
        if ($stmt->rowCount() > 0) {
            $aFasesCompletadas = [];
            foreach ($stmt as $aDades) {
                if (!is_array($aDades)) {
                    continue;
                }
                if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($aDades['completado'] ?? false)) {
                    if (isset($aDades['id_fase']) && is_numeric($aDades['id_fase'])) {
                        $aFasesCompletadas[] = (int) $aDades['id_fase'];
                    }
                }
            }
            return $aFasesCompletadas;
        }

        // no existe el proceso:
        $id_fase_primera = $this->generarProceso((string) $iid_activ);
        return is_int($id_fase_primera) ? [$id_fase_primera] : [];
    }
    public function faseCompletada(int $iid_activ, int $iid_fase): bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        // No puedo hacer la consulta con WHERE completado='t',
        // porque hay que distinguirlo de si existe el proceso o no, y hay que crearlo.
        $sQry = "SELECT * FROM $nom_tabla WHERE id_activ=$iid_activ AND id_fase=$iid_fase ";
        $stmt = $this->pdoQuery($oDbl, $sQry, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        if ($stmt->rowCount() == 1) {
            // aunque realmente solo debería existir un fila
            foreach ($stmt as $aDades) {
                if (!is_array($aDades)) {
                    continue;
                }
                return \src\shared\domain\helpers\FuncTablasSupport::isTrue($aDades['completado']) ?? false;
            }
        } else {
            // no existe el proceso:
            $this->generarProceso((string) $iid_activ);
            return false;
        }
        return false;
    }

    /**
     * Borra el proceso para la actividad indicada.
     * Si se le pasa el parámetro isfsv, sólo borra en la tabla correspondiente.
     * Si no, borra en las dos (sv y sf).
     */
    public function borrarProceso(int $iid_activ, ?int $isfsv = null): void
    {
        if (empty($isfsv)) {
            $a_sfsv = [1, 2];
        } else {
            $a_sfsv = [$isfsv];
        }
        foreach ($a_sfsv as $sfsv) {
            if ($sfsv === 1) {
                $this->setNomTabla('a_actividad_proceso_sv');
            } else {
                $this->setNomTabla('a_actividad_proceso_sf');
            }
            $this->borrar($iid_activ);
        }
    }

    /**
     * Borra el procés per l'activitat de la tabla activa.
     *
     * @param int $iid_activ
     */
    private function borrar(int $iid_activ): void
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (!empty($iid_activ)) {
            $sQry = "DELETE FROM $nom_tabla WHERE id_activ=$iid_activ";
            $this->pdoQuery($oDbl, $sQry, __METHOD__, __FILE__, __LINE__);
        }
    }

    /**
     * Genera el procés per l'activitat, segons el tipus de procés.
     * retorna el id_fase de la primera fase.
     *
     * @param int $iid_activ
     * @param int $iid_tipo_proceso
     * @param int|null $isfsv
     * @return int id_fase.
     */
    private function generar(
        int $iid_activ,
        int $iid_tipo_proceso,
        int|null $isfsv,
        bool $force = false,
        ?ActividadAll $oActividad = null,
    ): int {
        $this->borrar($iid_activ);

        // ordena por fases previas, no importa la fase: simplemente las vacias primero
        // para saber cual es la primera: que no depende de ninguna.
        $aWhere = [
            'id_tipo_proceso' => $iid_tipo_proceso,
            '_ordre' => '(json_fases_previas::json->0)::text DESC'
        ];
        $cTareasProceso = $this->tareaProcesoRepository->getTareasProceso($aWhere);

        // OJO: cuando se accede actividades ya existentes,
        // hay que intentar conservar el status que tenga. Para las actividades anteriores
        // (antes de instalar el módulo de procesos), se marcarán todas las fases del status.
        // Para las posteriores, sólo la primera fase del status.
        // Vamos a establecer la fecha de hoy como criterio para distinguir entre
        // actividades anteriores y posteriores.
        if ($oActividad === null) {
            $oActividad = $this->findActividadForProceso((int) $iid_activ);
        }
        if ($oActividad === null) {
            $this->registrarAvisoGenerarProceso(sprintf(_("La actividad: %s ya no existe"), $iid_activ));
            return 0;
        }
        $nom_activ = $oActividad->getNom_activ();
        $statusActividad = $oActividad->getStatusVo()->value();

        // Si es borrable, hay que ver que hacemos: de momento nada.
        if ($statusActividad === StatusId::BORRABLE) {
            $nom_activ = empty($nom_activ) ? $iid_activ : $nom_activ;
            $this->registrarAvisoGenerarProceso(
                sprintf(_("error al generar el proceso de la actividad: '%s'. Está para borrar."), $nom_activ)
            );
            return 0;
        }
        // Si es anterior a hoy, mantengo el status de la actividad.
        $oFini = $oActividad->getF_ini();
        $oHoy = new DateTimeLocal();
        if ($oFini < $oHoy && $force === FALSE) {
            // Anterior
            foreach ($cTareasProceso as $oTareaProceso) {
                $id_fase = $oTareaProceso->getIdFaseVo()->value();
                $id_tarea = $oTareaProceso->getIdTareaVo()->value();
                $statusFase = $oTareaProceso->getStatusVo()->value();
                if ($statusFase <= $statusActividad) {
                    $completado = true;
                } else {
                    $completado = false;
                }
                $newIdItem = $this->getNewId();
                $oActividadProcesoTarea = new ActividadProcesoTarea();
                $oActividadProcesoTarea->setId_item($newIdItem);
                //??? $oActividadProcesoTarea->setSfsvVo($isfsv);
                    $oActividadProcesoTarea->setIdTipoProcesoVo($iid_tipo_proceso);
                    $oActividadProcesoTarea->setIdActividadVo($iid_activ);
                $oActividadProcesoTarea->setIdFaseVo($id_fase);
                $oActividadProcesoTarea->setIdTareaVo($id_tarea);
                $oActividadProcesoTarea->setCompletado(\src\shared\domain\helpers\FuncTablasSupport::isTrue($completado));
                if ($this->Guardar($oActividadProcesoTarea) === false) {
                    $this->registrarAvisoGenerarProceso(
                        "1.error: No se ha guardado el proceso: $iid_activ,$iid_tipo_proceso,$id_fase,$id_tarea"
                    );
                }
            }
        } else {
            // Posterior.
            // para el caso de forzar, pongo todo a 0.
            if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($force)) {
                $p = 0;
                $statusNew = '';
                foreach ($cTareasProceso as $oTareaProceso) {
                    $p++;
                    $id_fase = $oTareaProceso->getIdFaseVo()->value();
                    $id_tarea = $oTareaProceso->getIdTareaVo()->value();
                    $statusFase = $oTareaProceso->getStatusVo()->value();
                    $newIdItem = $this->getNewId();
                    $oActividadProcesoTarea = new ActividadProcesoTarea();
                    $oActividadProcesoTarea->setId_item($newIdItem);
                    //??? $oActividadProcesoTarea->setSfsvVo($isfsv);
                    $oActividadProcesoTarea->setIdTipoProcesoVo($iid_tipo_proceso);
                    $oActividadProcesoTarea->setIdActividadVo($iid_activ);
                    $oActividadProcesoTarea->setIdFaseVo($id_fase);
                    $oActividadProcesoTarea->setIdTareaVo($id_tarea);
                    if ($p === 1) {
                        $oActividadProcesoTarea->setCompletado(true); // Marco la primera fase como completado.
                        // marco el status correspondiente en la actividad. Hay que hacerlo al final para no entrar en
                        // un bucle recurrente al modificar una actividad nueva que todavía no tienen el proceso.
                        $statusNew = $statusFase;
                    }
                    if ($this->Guardar($oActividadProcesoTarea) === false) {
                        $this->registrarAvisoGenerarProceso(
                            "2.error: No se ha guardado el proceso: $iid_activ,$iid_tipo_proceso,$id_fase,$id_tarea"
                        );
                    }
                }
                // cambiar el status de la actividad para que se ajuste al de la fase.
                $dl_org = $oActividad->getDl_org() ?? '';
                $dl_org_no_f = preg_replace('/(\.*)f$/', '\1', $dl_org);
                // El status solo se puede guardar si la actividad es de la propia dl (o des desde sv).
                $oPerm = $_SESSION['oPerm'] ?? null;
                if ($dl_org_no_f === ConfigGlobal::mi_delef() && $oPerm instanceof \src\permisos\domain\XPermisos && $oPerm->have_perm_oficina('des')) {
                    $oActividad->setStatusVo((int) $statusNew);
                    $this->actividadAllRepository->Guardar($oActividad, false); // registrarCambios=false, para que no anote el cambio.
                }
            } else {
                // conservo el status
                // al hacer 'insert' no marca dependencias (sólo con 'update').
                // por tanto doy dos vueltas, una para crear las fases y otra para marcar las completadas
                foreach ($cTareasProceso as $oTareaProceso) {
                    $id_fase = $oTareaProceso->getIdFaseVo()->value();
                    $id_tarea = $oTareaProceso->getIdTareaVo()->value();
                    $statusFase = $oTareaProceso->getStatusVo()->value();
                    $newIdItem = $this->getNewId();
                    $oActividadProcesoTarea = new ActividadProcesoTarea();
                    $oActividadProcesoTarea->setId_item($newIdItem);
                    //??? $oActividadProcesoTarea->setSfsvVo($isfsv);
                    $oActividadProcesoTarea->setIdTipoProcesoVo($iid_tipo_proceso);
                    $oActividadProcesoTarea->setIdActividadVo($iid_activ);
                    $oActividadProcesoTarea->setIdFaseVo($id_fase);
                    $oActividadProcesoTarea->setIdTareaVo($id_tarea);
                    if ($this->Guardar($oActividadProcesoTarea) === false) {
                        $this->registrarAvisoGenerarProceso(
                            "3.error: No se ha guardado el proceso: $iid_activ,$iid_tipo_proceso,$id_fase,$id_tarea"
                        );
                    }
                }
                $aWhere = [
                    'id_activ' => $iid_activ,
                    '_ordre' => 'id_fase',
                ];
                $cActividadProcesoTarea = $this->getActividadProcesoTareas($aWhere);
                foreach ($cActividadProcesoTarea as $oActividadProcesoTarea) {
                    $id_fase = $oActividadProcesoTarea->getIdFaseVo()?->value();
                    $id_tarea = $oActividadProcesoTarea->getIdTareaVo()?->value();
                    $completado = 'f';
                    // marco el status correspondiente en la actividad.
                    if ($statusActividad === StatusId::PROYECTO) {
                        if ($id_fase <= FaseId::FASE_PROYECTO) {
                            $completado = 't';
                        }
                    }
                    if ($statusActividad === StatusId::ACTUAL) {
                        if ($id_fase <= FaseId::FASE_APROBADA) {
                            $completado = 't';
                        }
                    }
                    if ($statusActividad === StatusId::TERMINADA) {
                        if ($id_fase <= FaseId::FASE_TERMINADA) {
                            $completado = 't';
                        }
                    }
                    if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($completado)) {
                        $oActividadProcesoTarea->setCompletado(\src\shared\domain\helpers\FuncTablasSupport::isTrue($completado));
                        if ($this->Guardar($oActividadProcesoTarea) === false) {
                            $this->registrarAvisoGenerarProceso(
                                "4.error: No se ha guardado el proceso: $iid_activ,$iid_tipo_proceso,$id_fase,$id_tarea"
                            );
                        }
                    }
                }
            }
        }

        if (!empty($cTareasProceso[0])) {
            return $cTareasProceso[0]->getIdFaseVo()->value();
        } else {
            $oProcesoTipo = $this->procesoTipoRepository->findById((int) $iid_tipo_proceso);
            $nom_proceso = ($oProcesoTipo === null || $oProcesoTipo->getNom_proceso() === '') ? $iid_tipo_proceso : $oProcesoTipo->getNom_proceso();
            $nom_activ = empty($nom_activ) ? $iid_activ : $nom_activ;

            $msg = sprintf(_("error al generar el proceso de la actividad: '%s'. Tipo de proceso: '%s' para sf/sv: %s."), $nom_activ, $nom_proceso, $isfsv);
            $msg .= ' ' . _("Probablemente no esté definido el proceso");
            $this->registrarAvisoGenerarProceso($msg);
        }
        return 0;
    }

    /**
     * Creo esta nueva función para poder guardar sin volver a repetir el proceso de mirar si hay que cambiar el
     * estado de la actividad. Sólo sirve para hacer UPDATE de completada.
     *
     */
    public function DBMarcar(ActividadProcesoTarea $ActividadProcesoTarea): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $id_item = $ActividadProcesoTarea->getId_item();

        $aDades = [];
        $aDades['id_tipo_proceso'] = $ActividadProcesoTarea->getIdTipoProcesoVo()->value();
        $aDades['id_activ'] = $ActividadProcesoTarea->getId_activ();
        $aDades['id_fase'] = $ActividadProcesoTarea->getIdFaseVo()?->value();
        $aDades['id_tarea'] = $ActividadProcesoTarea->getIdTareaVo()?->value();
        $aDades['completado'] = $ActividadProcesoTarea->isCompletado();
        $aDades['observ'] = $ActividadProcesoTarea->getObservVo()?->value();
        array_walk($aDades, [\src\shared\domain\helpers\FuncTablasSupport::class, 'ponerNull']);
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($aDades['completado'])) {
            $aDades['completado'] = 'true';
        } else {
            $aDades['completado'] = 'false';
        }

        //UPDATE
        $update = "
                id_tipo_proceso          = :id_tipo_proceso,
                id_activ                 = :id_activ,
                id_fase                  = :id_fase,
                id_tarea                 = :id_tarea,
                completado               = :completado,
                observ                   = :observ";
        if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$id_item'")) === FALSE) {
            $sClauError = 'ActividadProcesoTarea.update.prepare';
            if (($_SESSION['oGestorErrores'] ?? null) instanceof GestorErrores) {
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, (string) __LINE__, __FILE__);
            }
            return false;
        } else {
            try {
                $oDblSt->execute($aDades);
            } catch (\PDOException $e) {
                $err_txt = $e->errorInfo[2] ?? $e->getMessage();
                $this->setErrorTxt(is_string($err_txt) ? $err_txt : $e->getMessage());
                $sClauError = 'ActividadProcesoTarea.update.execute';
                if (($_SESSION['oGestorErrores'] ?? null) instanceof GestorErrores) {
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, (string) __LINE__, __FILE__);
                }
                return false;
            }
        }
        return TRUE;
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ActividadProcesoTarea
     *
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<ActividadProcesoTarea> Una colección de objetos de tipo ActividadProcesoTarea
     */
    public function getActividadProcesoTareas(array $aWhere = [], array $aOperators = []): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $ActividadProcesoTareaSet = new Set();
        $oCondicion = new Condicion();
        $aCondicion = [];
        foreach ($aWhere as $camp => $val) {
            if ($camp === '_ordre') {
                continue;
            }
            if ($camp === '_limit') {
                continue;
            }
            $sOperador = $aOperators[$camp] ?? '';
            if ($a = $oCondicion->getCondicion($camp, $sOperador, $val)) {
                $aCondicion[] = $a;
            }
            // operadores que no requieren valores
            if ($sOperador === 'BETWEEN' || $sOperador === 'IS NULL' || $sOperador === 'IS NOT NULL' || $sOperador === 'OR') {
                unset($aWhere[$camp]);
            }
            if ($sOperador === 'IN' || $sOperador === 'NOT IN') {
                unset($aWhere[$camp]);
            }
            if ($sOperador === 'TXT') {
                unset($aWhere[$camp]);
            }
        }
        $sCondicion = implode(' AND ', $aCondicion);
        if ($sCondicion !== '') {
            $sCondicion = " WHERE " . $sCondicion;
        }
        $sOrdre = '';
        $sLimit = '';
        $ordreVal = $aWhere['_ordre'] ?? null;
        if (is_string($ordreVal) && $ordreVal !== '') {
            $sOrdre = ' ORDER BY ' . $ordreVal;
        }
        if (isset($aWhere['_ordre'])) {
            unset($aWhere['_ordre']);
        }
        $limitVal = $aWhere['_limit'] ?? null;
        if ((is_string($limitVal) || is_int($limitVal)) && (string) $limitVal !== '') {
            $sLimit = ' LIMIT ' . $limitVal;
        }
        if (isset($aWhere['_limit'])) {
            unset($aWhere['_limit']);
        }
        $sQry = "SELECT * FROM $nom_tabla " . $sCondicion . $sOrdre . $sLimit;
        $stmt = $this->prepareAndExecute($oDbl, $sQry, $aWhere, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        /** @var list<ActividadProcesoTarea> $items */
        $items = [];
        foreach ($filas as $aDatos) {
            if (!is_array($aDatos)) {
                continue;
            }
            $normalized = [];
            foreach ($aDatos as $key => $value) {
                $normalized[(string) $key] = $value;
            }
            $items[] = ActividadProcesoTarea::fromArray($normalized);
        }
        return $items;
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ActividadProcesoTarea $ActividadProcesoTarea): bool
    {
        $id_item = $ActividadProcesoTarea->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item = $id_item";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(ActividadProcesoTarea $ActividadProcesoTarea): bool
    {
        $id_item = $ActividadProcesoTarea->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item);

        $aDatos = $ActividadProcesoTarea->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_item']);
            $update = "
					id_tipo_proceso          = :id_tipo_proceso,
					id_activ                 = :id_activ,
					id_fase                  = :id_fase,
					id_tarea                 = :id_tarea,
					completado               = :completado,
					observ                   = :observ";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_item = $id_item";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $campos = "(id_item,id_tipo_proceso,id_activ,id_fase,id_tarea,completado,observ)";
            $valores = "(:id_item,:id_tipo_proceso,:id_activ,:id_fase,:id_tarea,:completado,:observ)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        /** @var \PDOStatement $stmt */
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_item): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item = $id_item";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return true;
        }
        if (!$stmt->rowCount()) {
            return TRUE;
        }
        return false;
    }

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_item
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_item): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item = $id_item";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDatos)) {
            return false;
        }
        $result = [];
        foreach ($aDatos as $key => $value) {
            $result[(string) $key] = $value;
        }

        return $result;
    }


    /**
     * Busca la clase con id_item en la base de datos .
     */
    public function findById(int $id_item): ?ActividadProcesoTarea
    {
        $aDatos = $this->datosById($id_item);
        if ($aDatos === false) {
            return null;
        }
        return ActividadProcesoTarea::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('a_actividad_proceso_sv_id_item_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}