<?php

namespace misas\domain\repositories;

use misas\domain\EncargoDiaId;
use misas\domain\entity\EncargoDia;
use misas\infrastructure\PgEncargoDiaRepository;
use PDO;

/**
 *
 * Clase para gestionar la lista de objetos tipo Plantilla
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/3/2023
 */
class EncargoDiaRepository implements EncargoDiaRepositoryInterface
{

    private EncargoDiaRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = $GLOBALS['container']->get(PgEncargoDiaRepositoryInterface::class);
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Plantilla
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo Plantilla
     */
    public function getEncargoDias(array $aWhere = [], array $aOperators = []): array|false
    {
        return $this->repository->getEncargoDias($aWhere, $aOperators);
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(EncargoDia $EncargoDia): bool
    {
        return $this->repository->Eliminar($EncargoDia);
    }

    public function Guardar(EncargoDia $EncargoDia): bool
    {
        return $this->repository->Guardar($EncargoDia);
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
     */
    public function datosById(EncargoDiaId $uuid_item): array|bool
    {
        return $this->repository->datosById($uuid_item);
    }

    /**
     * Busca la clase con id_item en el repositorio.
     */
    public function findById(EncargoDiaId $uuid_item): ?EncargoDia
    {
        return $this->repository->findById($uuid_item);
    }

}