<?php

namespace misas\domain\repositories;

use misas\domain\EncargoCtrId;
use misas\domain\entity\EncargoCtr;
use misas\infrastructure\PgEncargoCtrRepository;
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
class EncargoCtrRepository implements EncargoCtrRepositoryInterface
{

    private EncargoCtrRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgEncargoCtrRepository();
    }

    public function getCentrosEncargo(int $id_enc): array|false
    {
        return $this->repository->getCentrosEncargo($id_enc);
    }

    public function getEncargosCentro(int $id_ubi): array|false
    {
        return $this->repository->getEncargosCentro($id_ubi);
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos EncargoCtr
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos EncargoCtr
     */
    public function getEncargosCentros(array $aWhere = [], array $aOperators = []): array|false
    {
        return $this->repository->getEncargosCentros($aWhere, $aOperators);
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(EncargoCtr $EncargoCtr): bool
    {
        return $this->repository->Eliminar($EncargoCtr);
    }

    public function Guardar(EncargoCtr $EncargoCtr): bool
    {
        return $this->repository->Guardar($EncargoCtr);
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
    public function datosById(EncargoCtrId $uuid_item): array|bool
    {
        return $this->repository->datosById($uuid_item);
    }

    /**
     * Busca la clase con id_item en el repositorio.
     */
    public function findById(EncargoCtrId $uuid_item): ?EncargoCtr
    {
        return $this->repository->findById($uuid_item);
    }

}