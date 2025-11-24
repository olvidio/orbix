<?php

namespace src\ubis\application\repositories;

use PDO;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\entity\Casa;
use src\ubis\infrastructure\repositories\PgCasaDlRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo Casa
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/11/2025
 */
class CasaDlRepository implements CasaRepositoryInterface
{

    /**$
     * @var CasaRepositoryInterface
     */
    private CasaRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgCasaDlRepository();
    }

    public function getArrayCasas($sCondicion = ''): array
    {
        return $this->repository->getArrayCasas($sCondicion);
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Casa
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo Casa
     */
    public function getCasas(array $aWhere = [], array $aOperators = []): array|false
    {
        return $this->repository->getCasas($aWhere, $aOperators);
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Casa $Casa): bool
    {
        return $this->repository->Eliminar($Casa);
    }

    public function Guardar(Casa $Casa): bool
    {
        return $this->repository->Guardar($Casa);
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
     * @param int $id_ubi
     * @return array|bool
     */
    public function datosById(int $id_ubi): array|bool
    {
        return $this->repository->datosById($id_ubi);
    }

    /**
     * Busca la clase con id_ubi en el repositorio.
     */
    public function findById(int $id_ubi): ?Casa
    {
        return $this->repository->findById($id_ubi);
    }

    public function getNewId(): int
    {
        return $this->repository->getNewId();
    }

    public function getNewIdUbi($id): int
    {
        return $this->repository->getNewIdUbi($id);
    }

}