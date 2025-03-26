<?php

namespace inventario\domain\repositories;

use inventario\domain\entity\Equipaje;
use inventario\infrastructure\PgEquipajeRepository;
use PDO;
use web\Desplegable;


/**
 *
 * Clase para gestionar la lista de objetos tipo Equipaje
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
class EquipajeRepository implements EquipajeRepositoryInterface
{

    /**$
     * @var EquipajeRepositoryInterface
     */
    private EquipajeRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgEquipajeRepository();
    }


    public function getEquipajesCoincidentes(string $f_ini_iso, string $f_fin_iso): array
    {
        return $this->repository->getEquipajesCoincidentes($f_ini_iso, $f_fin_iso);
    }

    public function getArrayEquipajes(string $f_ini_iso = ''): array
    {
        return $this->repository->getArrayEquipajes($f_ini_iso);
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Equipaje
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo Equipaje
     */
    public function getEquipajes(array $aWhere = [], array $aOperators = []): array|false
    {
        return $this->repository->getEquipajes($aWhere, $aOperators);
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Equipaje $Equipaje): bool
    {
        return $this->repository->Eliminar($Equipaje);
    }

    public function Guardar(Equipaje $Equipaje): bool
    {
        return $this->repository->Guardar($Equipaje);
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
     * @param int $id_equipaje
     * @return array|bool
     */
    public function datosById(int $id_equipaje): array|bool
    {
        return $this->repository->datosById($id_equipaje);
    }

    /**
     * Busca la clase con id_equipaje en el repositorio.
     */
    public function findById(int $id_equipaje): ?Equipaje
    {
        return $this->repository->findById($id_equipaje);
    }

    public function getNewId()
    {
        return $this->repository->getNewId();
    }
}