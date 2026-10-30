<?php

namespace src\procesos\domain\contracts;

use src\shared\domain\criteria\Criteria;
use JsonException;
use src\procesos\domain\entity\TareaProceso;


/**
 * Interfaz de la clase TareaProceso y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 26/12/2025
 */
interface TareaProcesoRepositoryInterface
{

    /**
     * @return array<string, list<array<string, string>>>
     */
    public function getArrayFasesDependientes(int $iid_tipo_proceso): array;

    /**
     * @return array<string, array<string, string>>
     */
    public function arbolPrevio(int $iid_tipo_proceso): array;

    /**
     * @param array<string, bool> $aFasesEstado
     */
    public function getStatusProceso(int $iid_tipo_proceso, array $aFasesEstado): int;

    /**
     * @return array<int, int>
     */
    public function getFasesProceso(int $iid_tipo_proceso): array;

    /**
     * @return list<TareaProceso>
     */
    public function getFaseIndependiente(int $id_tipo_proceso): array;


    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * @return list<TareaProceso>
     */
    public function matching(Criteria $criteria): array;

    /**
     * Tareas de un tipo de proceso, con las de fases previas vacías primero.
     *
     * @return list<TareaProceso>
     */
    public function getTareasDeProcesoVaciasPrimero(int $id_tipo_proceso): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(TareaProceso $TareaProceso): bool;

    public function Guardar(TareaProceso $TareaProceso): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_item
     * @return array<string, mixed>|false
     * @throws JsonException
     */
    public function datosById(int $id_item): array|false;

    /**
     * Busca la clase con id_item en el repositorio.
     * @throws JsonException
     */
    public function findById(int $id_item): ?TareaProceso;

    public function getNewId(): int;
}