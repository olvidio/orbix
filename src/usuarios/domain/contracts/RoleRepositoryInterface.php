<?php

namespace src\usuarios\domain\contracts;

use PDO;
use src\usuarios\domain\entity\Role;

/**
 * Interfaz de la clase Role y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/4/2025
 */
interface RoleRepositoryInterface
{

    public function getArrayRoles(): array;

    public function getArrayRolesPau(): array;

    public function getArrayRolesCondicion(string $sWhere = ''): array;
    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Role
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo Role
     */
    public function getRoles(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Role $Role): bool;

    public function Guardar(Role $Role): bool;

    public function getErrorTxt(): string;

    public function getoDbl(): PDO;

    public function setoDbl(PDO $oDbl): void;

    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_role
     * @return array|bool
     */
    public function datosById(int $id_role): array|bool;

    /**
     * Busca la clase con id_role en el repositorio.
     */
    public function findById(int $id_role): ?Role;

    public function getNewId();
}