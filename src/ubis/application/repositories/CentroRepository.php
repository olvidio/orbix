<?php

namespace src\ubis\application\repositories;

use PDO;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\entity\Centro;
use src\ubis\infrastructure\repositories\PgCentroRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo Centro
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/11/2025
 */
class CentroRepository implements CentroRepositoryInterface
{

    /**$
     * @var CentroRepositoryInterface
     */
    private CentroRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgCentroRepository();
    }


    public function getArrayCentrosCdc(string $condicion = ''): array
    {
       return $this->repository->getArrayCentrosCdc($condicion);
    }

    public function getArrayCentros(string $condicion = ''): array
    {
        return $this->repository->getArrayCentros($condicion);
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Centro
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo Centro
     */
    public function getCentros(array $aWhere = [], array $aOperators = []): array|false
    {
        return $this->repository->getCentros($aWhere, $aOperators);
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Centro $Centro): bool
    {
        return $this->repository->Eliminar($Centro);
    }

    public function Guardar(Centro $Centro): bool
    {
        return $this->repository->Guardar($Centro);
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
    public function findById(int $id_ubi): ?Centro
    {
        return $this->repository->findById($id_ubi);
    }
}