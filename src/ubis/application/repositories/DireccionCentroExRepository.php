<?php

namespace src\ubis\application\repositories;

use PDO;
use src\ubis\domain\contracts\DireccionRepositoryInterface;
use src\ubis\domain\entity\Direccion;
use src\ubis\infrastructure\repositories\PgDireccionCentroExRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo Direccion
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 21/11/2025
 */
class DireccionCentroExRepository implements DireccionRepositoryInterface
{

    /**$
     * @var DireccionRepositoryInterface
     */
    private DireccionRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgDireccionCentroExRepository();
    }

    public function getArrayPoblaciones($sCondicion = ''): array
    {
        return $this->repository->getArrayPoblaciones($sCondicion);
    }

    public function getArrayPaises($sCondicion = ''): array
    {
        return $this->repository->getArrayPaises($sCondicion);
    }
    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Direccion
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo Direccion
     */
    public function getDirecciones(array $aWhere = [], array $aOperators = []): array|false
    {
        return $this->repository->getDirecciones($aWhere, $aOperators);
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Direccion $Direccion): bool
    {
        return $this->repository->Eliminar($Direccion);
    }

    public function Guardar(Direccion $Direccion): bool
    {
        return $this->repository->Guardar($Direccion);
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
     * @param int $id_direccion
     * @return array|bool
     */
    public function datosById(int $id_direccion): array|bool
    {
        return $this->repository->datosById($id_direccion);
    }

    /**
     * Busca la clase con id_direccion en el repositorio.
     */
    public function findById(int $id_direccion): ?Direccion
    {
        return $this->repository->findById($id_direccion);
    }

    public function getNewId(): int
    {
        return $this->repository->getNewId();
    }

    public function getNewIdDireccion($id)
    {
        return $this->repository->getNewIdDireccion($id);
    }
}