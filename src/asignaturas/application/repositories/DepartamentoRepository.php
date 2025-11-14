<?php

namespace src\asignaturas\application\repositories;

use PDO;
use src\asignaturas\domain\contracts\DepartamentoRepositoryInterface;
use src\asignaturas\domain\entity\Departamento;
use src\asignaturas\infrastructure\repositories\PgDepartamentoRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo Departamento
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 14/11/2025
 */
class DepartamentoRepository implements DepartamentoRepositoryInterface
{

    /**$
     * @var DepartamentoRepositoryInterface
     */
    private DepartamentoRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgDepartamentoRepository();
    }

    public function getArrayDepartamentos(): array
    {
        return $this->repository->getArrayDepartamentos();
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Departamento
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo Departamento
     */
    public function getDepartamentos(array $aWhere = [], array $aOperators = []): array|false
    {
        return $this->repository->getDepartamentos($aWhere, $aOperators);
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Departamento $Departamento): bool
    {
        return $this->repository->Eliminar($Departamento);
    }

    public function Guardar(Departamento $Departamento): bool
    {
        return $this->repository->Guardar($Departamento);
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
     * @param int $id_departamento
     * @return array|bool
     */
    public function datosById(int $id_departamento): array|bool
    {
        return $this->repository->datosById($id_departamento);
    }

    /**
     * Busca la clase con id_departamento en el repositorio.
     */
    public function findById(int $id_departamento): ?Departamento
    {
        return $this->repository->findById($id_departamento);
    }

    public function getNewId()
    {
        return $this->repository->getNewId();
    }
}