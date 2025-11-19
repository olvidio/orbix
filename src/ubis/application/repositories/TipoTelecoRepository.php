<?php

namespace src\ubis\application\repositories;

use PDO;
use src\ubis\domain\contracts\TipoTelecoRepositoryInterface;
use src\ubis\domain\entity\TipoTeleco;
use src\ubis\infrastructure\repositories\PgTipoTelecoRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo TipoTeleco
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 17/11/2025
 */
class TipoTelecoRepository implements TipoTelecoRepositoryInterface
{

    /**$
     * @var TipoTelecoRepositoryInterface
     */
    private TipoTelecoRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgTipoTelecoRepository();
    }

    public function getArrayTiposTelecoPersona(): array
    {
        return $this->repository->getArrayTiposTelecoPersona();
    }

    public function getArrayTiposTelecoUbi(): array
    {
        return $this->repository->getArrayTiposTelecoUbi();
    }

    public function getArrayTiposTeleco(): array
    {
        return $this->repository->getArrayTiposTeleco();
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo TipoTeleco
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo TipoTeleco
     */
    public function getTiposTeleco(array $aWhere = [], array $aOperators = []): array|false
    {
        return $this->repository->getTiposTeleco($aWhere, $aOperators);
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(TipoTeleco $TipoTeleco): bool
    {
        return $this->repository->Eliminar($TipoTeleco);
    }

    public function Guardar(TipoTeleco $TipoTeleco): bool
    {
        return $this->repository->Guardar($TipoTeleco);
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
     * @param int $id
     * @return array|bool
     */
    public function datosById(int $id): array|bool
    {
        return $this->repository->datosById($id);
    }

    /**
     * Busca la clase con id en el repositorio.
     */
    public function findById(int $id): ?TipoTeleco
    {
        return $this->repository->findById($id);
    }

    public function getNewId()
    {
        return $this->repository->getNewId();
    }
}