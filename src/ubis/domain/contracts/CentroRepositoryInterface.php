<?php

namespace src\ubis\domain\contracts;

use src\ubis\domain\entity\Centro;
use src\shared\domain\criteria\Criteria;

/**
 * Interfaz de la clase Centro y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/11/2025
 */
interface CentroRepositoryInterface
{

    /**
     * @return array<int|string, string>
     */
    public function getArrayCentrosCdc(string $condicion=''): array;

    /**
     * @return array<int|string, string>
     */
    public function getArrayCentros(string $condicion = ''): array;

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * Centro que cumplen el criterio.
     *
     * @return list<Centro>
     */
    public function matching(Criteria $criteria): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Centro $Centro): bool;

    public function Guardar(Centro $Centro): bool;

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
    public function findById(int $id_ubi): ?Centro;
}