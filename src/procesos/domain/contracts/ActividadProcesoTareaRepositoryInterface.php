<?php

namespace src\procesos\domain\contracts;

use src\actividades\domain\entity\ActividadAll;
use src\procesos\domain\entity\ActividadProcesoTarea;


/**
 * Interfaz de la clase ActividadProcesoTarea y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 26/12/2025
 */
interface ActividadProcesoTareaRepositoryInterface
{

    public function addFaseTarea(int $id_tipo_proceso, int $id_fase, int $id_tarea): void;

    public function getListaFaseEstado(int $iid_activ): array;
    public function getSacdAprobado(int $iid_activ): ?bool;


    public function getFasesCompletadas(int $iid_activ): array;
    public function faseCompletada(int $iid_activ, int $iid_fase): bool;



    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ActividadProcesoTarea
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array Una colección de objetos de tipo ActividadProcesoTarea
     */
    public function getActividadProcesoTareas(array $aWhere = [], array $aOperators = []): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ActividadProcesoTarea $ActividadProcesoTarea): bool;

    public function Guardar(ActividadProcesoTarea $ActividadProcesoTarea): bool;

    public function DBMarcar(ActividadProcesoTarea $ActividadProcesoTarea): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_item
     * @return array|bool
     */
    public function datosById(int $id_item): array|bool;

    /**
     * Busca la clase con id_item en el repositorio.
     */
    public function findById(int $id_item): ?ActividadProcesoTarea;

    public function getNewId();

    /**
     * Regenera las tareas del proceso para la actividad indicada.
     *
     * @param string $iid_activ
     * @param int|string $isfsv
     * @param bool $force
     * @param ActividadAll|null $oActividad Actividad ya cargada (p. ej. recién guardada en dl)
     * @return bool|int id_fase u otro valor según implementación
     */
    public function generarProceso(
        string $iid_activ = '',
        int|string $isfsv = '',
        bool $force = false,
        ?ActividadAll $oActividad = null,
    ): bool|int;

    /**
     * Avisos no fatales acumulados por la última llamada a {@see generarProceso()} (se vacían al leer).
     *
     * @return list<string>
     */
    public function consumirAvisosGenerarProceso(): array;
}