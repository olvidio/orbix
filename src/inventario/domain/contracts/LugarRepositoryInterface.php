<?php

namespace src\inventario\domain\contracts;

use src\inventario\domain\entity\Lugar;

/**
 * Interfaz de la clase Lugar y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
interface LugarRepositoryInterface
{

    /**
     * @return array<int|string, string>
     */
    /**
     * @return array<int|string, string>
     */
    public function getArrayLugares(int $id_ubi): array;

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Lugar
     *
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<Lugar> Una colección de objetos de tipo Lugar
     */
    public function getLugares(array $aWhere = [], array $aOperators = []): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Lugar $Lugar): bool;

    public function Guardar(Lugar $Lugar): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_lugar
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_lugar): array|false;

    /**
     * Busca la clase con id_lugar en el repositorio.
     */
    public function findById(int $id_lugar): ?Lugar;

    public function getNewId(): int;
}