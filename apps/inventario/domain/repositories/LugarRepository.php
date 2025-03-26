<?php

namespace inventario\domain\repositories;

use inventario\domain\entity\Lugar;
use inventario\infrastructure\PgLugarRepository;
use PDO;
use web\Desplegable;


/**
 *
 * Clase para gestionar la lista de objetos tipo Lugar
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
class LugarRepository implements LugarRepositoryInterface
{

    /**$
     * @var LugarRepositoryInterface
     */
    private LugarRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgLugarRepository();
    }


    public function getArrayLugares(int $id_ubi): array
    {
        return $this->repository->getArrayLugares($id_ubi);
    }
    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Lugar
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo Lugar
     */
    public function getLugares(array $aWhere = [], array $aOperators = []): array|false
    {
        return $this->repository->getLugares($aWhere, $aOperators);
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Lugar $Lugar): bool
    {
        return $this->repository->Eliminar($Lugar);
    }

    public function Guardar(Lugar $Lugar): bool
    {
        return $this->repository->Guardar($Lugar);
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
     * @param int $id_lugar
     * @return array|bool
     */
    public function datosById(int $id_lugar): array|bool
    {
        return $this->repository->datosById($id_lugar);
    }

    /**
     * Busca la clase con id_lugar en el repositorio.
     */
    public function findById(int $id_lugar): ?Lugar
    {
        return $this->repository->findById($id_lugar);
    }

    public function getNewId()
    {
        return $this->repository->getNewId();
    }
}