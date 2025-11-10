<?php

namespace src\ubis\application\repositories;

use PDO;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\entity\Delegacion;
use src\ubis\infrastructure\repositories\PgDelegacionRepository;

/**
 * Clase para gestionar la lista de objetos tipo Delegacion (capa application)
 * Delegando en el repositorio de infraestructura.
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 5/11/2025
 */
class DelegacionRepository implements DelegacionRepositoryInterface
{
    /**
     * @var DelegacionRepositoryInterface
     */
    private DelegacionRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgDelegacionRepository();
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Delegacion
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo Delegacion
     */
    public function getDelegaciones(array $aWhere = [], array $aOperators = []): array|false
    {
        return $this->repository->getDelegaciones($aWhere, $aOperators);
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Delegacion $Delegacion): bool
    {
        return $this->repository->Eliminar($Delegacion);
    }

    public function Guardar(Delegacion $Delegacion): bool
    {
        return $this->repository->Guardar($Delegacion);
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

    public function datosById(int $id_dl): array|bool
    {
        return $this->repository->datosById($id_dl);
    }

    /**
     * Busca la clase con dl en el repositorio.
     */
    public function findById(int $id_dl): ?Delegacion
    {
        return $this->repository->findById($id_dl);
    }

    /* -------------------- MÉTODOS ADICIONALES (legacy utilidades) ---------- */

    public function soy_region_stgr($dele = ''): bool
    {
        return $this->repository->soy_region_stgr($dele);
    }

    public function mi_region_stgr($dele = '')
    {
        return $this->repository->mi_region_stgr($dele);
    }

    public function getArrayIdSchemaRegionStgr($sRegionStgr, $mi_sfsv)
    {
        return $this->repository->getArrayIdSchemaRegionStgr($sRegionStgr, $mi_sfsv);
    }

    public function getArraySchemasRegionStgr($sRegionStgr, $mi_sfsv)
    {
        return $this->repository->getArraySchemasRegionStgr($sRegionStgr, $mi_sfsv);
    }

    public function getArrayDlRegionStgr($aRegiones = array())
    {
        return $this->repository->getArrayDlRegionStgr($aRegiones);
    }

}
