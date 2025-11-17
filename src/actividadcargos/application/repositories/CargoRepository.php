<?php

namespace src\actividadcargos\application\repositories;

use PDO;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividadcargos\domain\entity\Cargo;
use src\actividadcargos\infrastructure\repositories\PgCargoRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo Cargo
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 17/11/2025
 */
class CargoRepository implements CargoRepositoryInterface
{

    /**$
     * @var CargoRepositoryInterface
     */
    private CargoRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgCargoRepository();
    }

    public function getArrayCargos(string $tipo_cargo = ''): array
    {
        return $this->repository->getArrayCargos($tipo_cargo);
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Cargo
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo Cargo
     */
    public function getCargos(array $aWhere = [], array $aOperators = []): array|false
    {
        return $this->repository->getCargos($aWhere, $aOperators);
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Cargo $Cargo): bool
    {
        return $this->repository->Eliminar($Cargo);
    }

    public function Guardar(Cargo $Cargo): bool
    {
        return $this->repository->Guardar($Cargo);
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
     * @param int $id_cargo
     * @return array|bool
     */
    public function datosById(int $id_cargo): array|bool
    {
        return $this->repository->datosById($id_cargo);
    }

    /**
     * Busca la clase con id_cargo en el repositorio.
     */
    public function findById(int $id_cargo): ?Cargo
    {
        return $this->repository->findById($id_cargo);
    }

    public function getNewId()
    {
        return $this->repository->getNewId();
    }
}