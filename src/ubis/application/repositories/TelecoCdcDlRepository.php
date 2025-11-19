<?php

namespace src\ubis\application\repositories;

use PDO;
use src\ubis\domain\contracts\TelecoUbiRepositoryInterface;
use src\ubis\domain\entity\TelecoUbi;
use src\ubis\infrastructure\repositories\PgTelecoCdcDlRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo TelecoCdc
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 19/11/2025
 */
class TelecoCdcDlRepository implements TelecoUbiRepositoryInterface
{

    /**$
     * @var TelecoUbiRepositoryInterface
     */
    private TelecoUbiRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgTelecoCdcDlRepository();
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo TelecoCdc
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo TelecoCdc
     */
    public function getTelecos(array $aWhere = [], array $aOperators = []): array|false
    {
        return $this->repository->getTelecos($aWhere, $aOperators);
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(TelecoUbi $TelecoCdc): bool
    {
        return $this->repository->Eliminar($TelecoCdc);
    }

    public function Guardar(TelecoUbi $TelecoCdc): bool
    {
        return $this->repository->Guardar($TelecoCdc);
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
     * @param int $id_item
     * @return array|bool
     */
    public function datosById(int $id_item): array|bool
    {
        return $this->repository->datosById($id_item);
    }

    /**
     * Busca la clase con id_item en el repositorio.
     */
    public function findById(int $id_item): ?TelecoUbi
    {
        return $this->repository->findById($id_item);
    }

    public function getNewId()
    {
        return $this->repository->getNewId();
    }
}