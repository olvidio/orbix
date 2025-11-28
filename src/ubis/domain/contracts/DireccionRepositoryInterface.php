<?php

namespace src\ubis\domain\contracts;

use PDO;
use src\ubis\domain\entity\Direccion;

/**
 * Interfaz de la clase Direccion y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 21/11/2025
 */
interface DireccionRepositoryInterface
{
    public function getArrayPoblaciones($sCondicion = ''): array;

    public function getArrayPaises($sCondicion = ''): array;

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Direccion
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo Direccion
     */
    public function getDirecciones(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Direccion $Direccion): bool;

    public function Guardar(Direccion $Direccion): bool;

    public function getErrorTxt(): string;

    public function getoDbl(): PDO;

    public function setoDbl(PDO $oDbl): void;

    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_direccion
     * @return array|bool
     */
    public function datosById(int $id_direccion): array|bool;

    /**
     * Busca la clase con id_direccion en el repositorio.
     */
    public function findById(int $id_direccion): ?Direccion;
}