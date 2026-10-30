<?php

namespace src\ubis\domain\contracts;

use src\ubis\domain\entity\Direccion;
use src\shared\domain\criteria\Criteria;

/**
 * Interfaz de la clase Direccion y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 21/11/2025
 */
interface DireccionCasaRepositoryInterface
{
    /**
     * @return array<int|string, string>
     */
    public function getArrayPoblaciones(string $sCondicion = ''): array;

    /**
     * @return array<int|string, string>
     */
    public function getArrayPaises(string $sCondicion = ''): array;

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * Direccion que cumplen el criterio.
     *
     * @return list<Direccion>
     */
    public function matching(Criteria $criteria): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Direccion $Direccion): bool;

    public function Guardar(Direccion $Direccion): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_direccion
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
    public function datosById(int $id_direccion): array|false;

    /**
     * Busca la clase con id_direccion en el repositorio.
     */
    public function findById(int $id_direccion): ?Direccion;

    /**
     * @return array<string, mixed>
     */
    /**
     * @return array<string, mixed>
     */
    /**
     * @return array<string, mixed>
     */
    /**
     * @return array<string, mixed>
     */
    /**
     * @return array<string, mixed>
     */
    public function downloadPlano(int $id_direccion): array;
}