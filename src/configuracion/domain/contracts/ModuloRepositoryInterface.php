<?php

namespace src\configuracion\domain\contracts;

use src\configuracion\domain\entity\Modulo;
use src\shared\domain\criteria\Criteria;

/**
 * Interfaz de la clase Modulo y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 15/4/2025
 */
interface ModuloRepositoryInterface
{

    /**
     * @return array<int|string, string>
     */
    /**
     * @return array<int|string, string>
     */
    public function getArrayModulos(): array;

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * Módulos que cumplen el criterio.
     *
     * @return list<Modulo>
     */
    public function matching(Criteria $criteria): array;


    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Modulo $Modulo): bool;

    public function Guardar(Modulo $Modulo): bool;

    public function getErrorTxt(): string;


    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_mod
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_mod): array|false;

    /**
     * Busca la clase con id_mod en el repositorio.
     */
    public function findById(int $id_mod): ?Modulo;

    /**
     * @return int|string
     */
    /**
     * @return int|string
     */
    public function getNewId();
}