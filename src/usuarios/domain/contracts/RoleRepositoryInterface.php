<?php

namespace src\usuarios\domain\contracts;

use src\shared\domain\criteria\Criteria;
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

    /**
     * @return array<int|string, string>
     */
    public function getArrayRoles(): array;

    /**
     * @return array<int|string, string>
     */
    public function getArrayRolesPau(): array;

    /**
     * @return array<int|string, string>
     */
    public function getArrayRolesCondicion(string $sWhere = ''): array;
    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * @return list<Role>
     */
    public function matching(Criteria $criteria): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Role $Role): bool;

    public function Guardar(Role $Role): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_role
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_role): array|false;

    /**
     * Busca la clase con id_role en el repositorio.
     */
    public function findById(int $id_role): ?Role;

    public function getNewId(): int;
}