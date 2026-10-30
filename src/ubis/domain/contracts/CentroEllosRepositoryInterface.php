<?php

namespace src\ubis\domain\contracts;

use src\ubis\domain\entity\CentroEllos;
use src\shared\domain\criteria\Criteria;

/**
 * Interfaz de la clase CentroEllos y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 21/11/2025
 */
interface CentroEllosRepositoryInterface
{

    /**
     * @return array<int|string, string>
     */
    public function getArrayCentros(string $sCondicion = ''): array;
    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * CentroEllos que cumplen el criterio.
     *
     * @return list<CentroEllos>
     */
    public function matching(Criteria $criteria): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(CentroEllos $CentroEllos): bool;

    public function Guardar(CentroEllos $CentroEllos): bool;

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
    public function findById(int $id_ubi): ?CentroEllos;
}