<?php

namespace src\personas\application\repositories;

use PDO;
use src\personas\domain\contracts\SituacionRepositoryInterface;
use src\personas\domain\entity\Situacion;
use src\personas\infrastructure\repositories\PgSituacionRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo Situacion
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 17/11/2025
 */
class SituacionRepository implements SituacionRepositoryInterface
{

    /**$
     * @var SituacionRepositoryInterface
     */
    private SituacionRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgSituacionRepository();
    }

    public function getArraySituaciones($traslado = FALSE)
    {
        return $this->repository->getArraySituaciones($traslado);
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Situacion
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo Situacion
     */
    public function getSituaciones(array $aWhere = [], array $aOperators = []): array|false
    {
        return $this->repository->getSituaciones($aWhere, $aOperators);
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Situacion $Situacion): bool
    {
        return $this->repository->Eliminar($Situacion);
    }

    public function Guardar(Situacion $Situacion): bool
    {
        return $this->repository->Guardar($Situacion);
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
     * @param string $situacion
     * @return array|bool
     */
    public function datosById(string $situacion): array|bool
    {
        return $this->repository->datosById($situacion);
    }

    /**
     * Busca la clase con situacion en el repositorio.
     */
    public function findById(string $situacion): ?Situacion
    {
        return $this->repository->findById($situacion);
    }
}