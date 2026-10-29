<?php

namespace src\ubis\domain\contracts;

use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\entity\CasaPeriodo;
use src\shared\domain\criteria\Criteria;


/**
 * Interfaz de la clase CasaPeriodo y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 29/12/2025
 */
interface CasaPeriodoRepositoryInterface
{
    /**
     * @return list<array{iso_ini: string, iso_fin: string, sfsv: int}>
     */
    public function getArrayCasaPeriodos(int $id_ubi, DateTimeLocal $oInicio, DateTimeLocal $oFin): array;

    public function getCasaPeriodosDias(int $iseccion, int $id_ubi, DateTimeLocal $oInicio, DateTimeLocal $oFin): int;
    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * CasaPeriodo que cumplen el criterio.
     *
     * @return list<CasaPeriodo>
     */
    public function matching(Criteria $criteria): array;

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<CasaPeriodo>
     */
    public function getCasaPeriodos(array $aWhere = [], array $aOperators = []): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(CasaPeriodo $CasaPeriodo): bool;

    public function Guardar(CasaPeriodo $CasaPeriodo): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_item
     * @return array<string, mixed>|false
     */
    /**
     * @return array<string, mixed>|false
     */
    /**
     * @return array<string, mixed>|false
     */
    /**
     * @return array<string, mixed>|false
     */
    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_item): array|false;

    /**
     * Busca la clase con id_item en el repositorio.
     */
    public function findById(int $id_item): ?CasaPeriodo;

    public function getNewId(): int;
}