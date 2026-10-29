<?php

namespace src\ubis\domain\contracts;

use src\ubis\domain\entity\CentroDl;
use src\shared\domain\criteria\Criteria;
use src\ubis\domain\entity\Direccion;

/**
 * Interfaz de la clase CentroDl y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/11/2025
 */
interface CentroDlRepositoryInterface
{

    /**
     * @return array<int|string, string>
     */
    public function getArrayCentros(string $sCondicion = ''): array;

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * CentroDl que cumplen el criterio.
     *
     * @return list<CentroDl>
     */
    public function matching(Criteria $criteria): array;

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<CentroDl>
     */
    public function getCentros(array $aWhere = [], array $aOperators = []): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(CentroDl $CentroDl): bool;

    public function Guardar(CentroDl $CentroDl): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_ubi
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
    public function datosById(int $id_ubi): array|false;

    /**
     * Busca la clase con id_ubi en el repositorio.
     */
    public function findById(int $id_ubi): ?CentroDl;

    public function getNewId(): int;

    public function getNewIdUbi(int $id): int;
}