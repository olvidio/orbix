<?php

namespace src\cartaspresentacion\domain\contracts;

use PDO;
use src\cartaspresentacion\domain\entity\CartaPresentacion;


/**
 * Interfaz de la clase CartaPresentacion y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/12/2025
 */
interface CartaPresentacionRepositoryInterface
{

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo CartaPresentacion
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo CartaPresentacion
     */
    public function getCartasPresentacion(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(CartaPresentacion $CartaPresentacion): bool;

    public function Guardar(CartaPresentacion $CartaPresentacion): bool;

    public function getErrorTxt(): string;

    public function getoDbl(): PDO;

    public function setoDbl(PDO $oDbl): void;

    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_ubi
     * @param int $id_direccion
     * @return array|bool
     */
    public function datosById(int $id_ubi, int $id_direccion): array|bool;

    /**
     * Busca la clase con id_direccion en el repositorio.
     */
    public function findById(int $id_ubi, int $id_direccion): ?CartaPresentacion;
}