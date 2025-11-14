<?php

namespace src\asignaturas\application\repositories;

use PDO;
use src\asignaturas\domain\contracts\AsignaturaTipoRepositoryInterface;
use src\asignaturas\domain\entity\AsignaturaTipo;
use src\asignaturas\infrastructure\repositories\PgAsignaturaTipoRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo AsignaturaTipo
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 14/11/2025
 */
class AsignaturaTipoRepository implements AsignaturaTipoRepositoryInterface
{

    /**$
     * @var AsignaturaTipoRepositoryInterface
     */
    private AsignaturaTipoRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgAsignaturaTipoRepository();
    }

    function getArrayAsignaturaTipos(): array
    {
        return $this->repository->getArrayAsignaturaTipos();
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo AsignaturaTipo
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo AsignaturaTipo
     */
    public function getAsignaturaTipos(array $aWhere = [], array $aOperators = []): array|false
    {
        return $this->repository->getAsignaturaTipos($aWhere, $aOperators);
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(AsignaturaTipo $AsignaturaTipo): bool
    {
        return $this->repository->Eliminar($AsignaturaTipo);
    }

    public function Guardar(AsignaturaTipo $AsignaturaTipo): bool
    {
        return $this->repository->Guardar($AsignaturaTipo);
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
     * @param int $id_tipo
     * @return array|bool
     */
    public function datosById(int $id_tipo): array|bool
    {
        return $this->repository->datosById($id_tipo);
    }

    /**
     * Busca la clase con id_tipo en el repositorio.
     */
    public function findById(int $id_tipo): ?AsignaturaTipo
    {
        return $this->repository->findById($id_tipo);
    }
}