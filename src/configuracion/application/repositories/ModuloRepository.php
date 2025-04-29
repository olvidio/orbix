<?php

namespace src\configuracion\application\repositories;

use PDO;
use src\configuracion\domain\entity\Modulo;
use src\configuracion\domain\contracts\ModuloRepositoryInterface;
use src\configuracion\infrastructure\repositories\PgModuloRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo Modulo
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 15/4/2025
 */
class ModuloRepository implements ModuloRepositoryInterface
{

    /**$
     * @var ModuloRepositoryInterface
     */
    private ModuloRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgModuloRepository();
    }

    public function getArrayModulos(): array
    {
        return $this->repository->getArrayModulos();
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Modulo
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo Modulo
     */
    public function getModulos(array $aWhere = [], array $aOperators = []): array|false
    {
        return $this->repository->getModulos($aWhere, $aOperators);
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Modulo $Modulo): bool
    {
        return $this->repository->Eliminar($Modulo);
    }

    public function Guardar(Modulo $Modulo): bool
    {
        return $this->repository->Guardar($Modulo);
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
     * @param int $id_mod
     * @return array|bool
     */
    public function datosById(int $id_mod): array|bool
    {
        return $this->repository->datosById($id_mod);
    }

    /**
     * Busca la clase con id_mod en el repositorio.
     */
    public function findById(int $id_mod): ?Modulo
    {
        return $this->repository->findById($id_mod);
    }

    public function getNewId()
    {
        return $this->repository->getNewId();
    }
}