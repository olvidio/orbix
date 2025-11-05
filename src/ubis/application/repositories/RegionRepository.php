<?php

namespace src\ubis\application\repositories;

use PDO;
use src\ubis\domain\contracts\RegionRepositoryInterface;
use src\ubis\domain\entity\Region;
use src\ubis\infrastructure\repositories\PgRegionRepository;

/**
 * Clase para gestionar la lista de objetos tipo Region (capa application)
 * Delegando en el repositorio de infraestructura.
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 5/11/2025
 */
class RegionRepository implements RegionRepositoryInterface
{
    /**
     * @var RegionRepositoryInterface
     */
    private RegionRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgRegionRepository();
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Region
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo Region
     */
    public function getRegiones(array $aWhere = [], array $aOperators = []): array|false
    {
        return $this->repository->getRegiones($aWhere, $aOperators);
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Region $Region): bool
    {
        return $this->repository->Eliminar($Region);
    }

    public function Guardar(Region $Region): bool
    {
        return $this->repository->Guardar($Region);
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

    public function datosById(int $id_region): array|bool
    {
        return $this->repository->datosById($id_region);
    }

    /**
     * Busca la clase con region en el repositorio.
     */
    public function findById(int $id_region): ?Region
    {
        return $this->repository->findById($id_region);
    }

    public function getNewId()
    {
        return $this->repository->getNewId();
    }
}
