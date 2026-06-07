<?php

namespace src\configuracion\domain\contracts;

use src\configuracion\domain\entity\Modulo;

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
     * devuelve una colección (array) de objetos de tipo Modulo
     *
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<Modulo> Una colección de objetos de tipo Modulo
     */
    public function getModulos(array $aWhere = [], array $aOperators = []): array;

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