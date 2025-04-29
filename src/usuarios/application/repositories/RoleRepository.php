<?php

namespace src\usuarios\application\repositories;

use PDO;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\entity\Role;
use src\usuarios\infrastructure\repositories\PgRoleRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo Role
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/4/2025
 */
class RoleRepository implements RoleRepositoryInterface
{

    /**$
     * @var RoleRepositoryInterface
     */
    private RoleRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgRoleRepository();
    }

    public function getArrayRoles(): array
    {
        return $this->repository->getArrayRoles();
    }

    public function getArrayRolesPau(): array
    {
        return $this->repository->getArrayRolesPau();
    }

    public function getArrayRolesCondicion(string $sWhere = ''): array
    {
        return $this->repository->getArrayRolesCondicion($sWhere);
    }
    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Role
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo Role
     */
    public function getRoles(array $aWhere = [], array $aOperators = []): array|false
    {
        return $this->repository->getRoles($aWhere, $aOperators);
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Role $Role): bool
    {
        return $this->repository->Eliminar($Role);
    }

    public function Guardar(Role $Role): bool
    {
        return $this->repository->Guardar($Role);
    }

    public function getErrorTxt(): string
    {
        return $this->repository->getErrorTxt();
    }

    public function getoDbl(): PDO
    {
        return $this->repository->getoDbl();
    }

    public function setoDbl(PDO $oDbl): void
    {
        $this->repository->setoDbl($oDbl);
    }

    public function getNomTabla(): string
    {
        return $this->repository->getNomTabla();
    }

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_role
     * @return array|bool
     */
    public function datosById(int $id_role): array|bool
    {
        return $this->repository->datosById($id_role);
    }

    /**
     * Busca la clase con id_role en el repositorio.
     */
    public function findById(int $id_role): ?Role
    {
        return $this->repository->findById($id_role);
    }

    public function getNewId()
    {
        return $this->repository->getNewId();
    }
}