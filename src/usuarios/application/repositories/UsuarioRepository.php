<?php

namespace src\usuarios\application\repositories;

use PDO;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\entity\Usuario;
use src\usuarios\infrastructure\repositories\PgUsuarioRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo usuario
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/4/2025
 */
class UsuarioRepository implements UsuarioRepositoryInterface
{

    /**$
     * @var UsuarioRepositoryInterface
     */
    private UsuarioRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgUsuarioRepository();
    }

    public function getArrayUsuarios(): array
    {
        return $this->repository->getArrayUsuarios();
    }
    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo usuario
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo usuario
     */
    public function getUsuarios(array $aWhere = [], array $aOperators = []): array|false
    {
        return $this->repository->getUsuarios($aWhere, $aOperators);
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Usuario $usuario): bool
    {
        return $this->repository->Eliminar($usuario);
    }

    public function Guardar(Usuario $usuario): bool
    {
        return $this->repository->Guardar($usuario);
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

    public function getoDbl_select(): PDO
    {
        return $this->repository->getoDbl_Select();
    }

    public function setoDbl_select(PDO $oDbl_Select): void
    {
        $this->repository->setoDbl_Select($oDbl_Select);
    }

    public function getNomTabla(): string
    {
        return $this->repository->getNomTabla();
    }

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_usuario
     * @return array|bool
     */
    public function datosById(int $id_usuario): array|bool
    {
        return $this->repository->datosById($id_usuario);
    }

    /**
     * Busca la clase con id_usuario en el repositorio.
     */
    public function findById(int $id_usuario): ?Usuario
    {
        return $this->repository->findById($id_usuario);
    }

    public function getNewId()
    {
        return $this->repository->getNewId();
    }
}