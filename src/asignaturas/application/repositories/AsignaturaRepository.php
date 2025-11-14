<?php

namespace src\asignaturas\application\repositories;

use PDO;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\asignaturas\infrastructure\repositories\PgAsignaturaRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo Asignatura
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 14/11/2025
 */
class AsignaturaRepository implements AsignaturaRepositoryInterface
{

    /**$
     * @var AsignaturaRepositoryInterface
     */
    private AsignaturaRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgAsignaturaRepository();
    }



    public function getJsonAsignaturas($aWhere): string
    {
        return $this->repository->getJsonAsignaturas($aWhere);
    }

    public function getArrayAsignaturasCreditos(): array
    {
        return $this->repository->getArrayAsignaturasCreditos();
    }

    public function getArrayAsignaturasConSeparador(bool $op_genericas = true): array
    {
        return $this->repository->getArrayAsignaturasConSeparador($op_genericas);
    }

    public function getListaOpGenericas(string $formato = ''): string
    {
        return $this->repository->getListaOpGenericas($formato);
    }

    public function getArrayAsignaturas(): array
    {
        return $this->repository->getArrayAsignaturas();
    }

    public function getAsignaturasAsJson($aWhere = [], $aOperators = array()): string
    {
        return $this->repository->getAsignaturasAsJson($aWhere, $aOperators);
    }
    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Asignatura
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo Asignatura
     */
    public function getAsignaturas(array $aWhere = [], array $aOperators = []): array|false
    {
        return $this->repository->getAsignaturas($aWhere, $aOperators);
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Asignatura $Asignatura): bool
    {
        return $this->repository->Eliminar($Asignatura);
    }

    public function Guardar(Asignatura $Asignatura): bool
    {
        return $this->repository->Guardar($Asignatura);
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
     * @param int $id_asignatura
     * @return array|bool
     */
    public function datosById(int $id_asignatura): array|bool
    {
        return $this->repository->datosById($id_asignatura);
    }

    /**
     * Busca la clase con id_asignatura en el repositorio.
     */
    public function findById(int $id_asignatura): ?Asignatura
    {
        return $this->repository->findById($id_asignatura);
    }

    public function getNewId()
    {
        return $this->repository->getNewId();
    }
}