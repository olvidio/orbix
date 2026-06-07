<?php

namespace src\ubis\domain\contracts;

use src\ubis\domain\entity\Centro;

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
     * devuelve una colección (array) de objetos de tipo Centro
     *
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<Centro> Una colección de objetos de tipo Centro
     */
    public function getCentros(array $aWhere = [], array $aOperators = []): array;

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