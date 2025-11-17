<?php

namespace src\profesores\application\repositories;

use PDO;
use src\profesores\domain\contracts\ProfesorTipoRepositoryInterface;
use src\profesores\domain\entity\ProfesorTipo;
use src\profesores\infrastructure\repositories\PgProfesorTipoRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo ProfesorTipo
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 17/11/2025
 */
class ProfesorTipoRepository implements ProfesorTipoRepositoryInterface
{

    /**$
     * @var ProfesorTipoRepositoryInterface
     */
    private ProfesorTipoRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgProfesorTipoRepository();
    }

    public function getArrayProfesorTipos(): array
    {
        return $this->repository->getArrayProfesorTipos();
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ProfesorTipo
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo ProfesorTipo
     */
    public function getProfesorTipos(array $aWhere = [], array $aOperators = []): array|false
    {
        return $this->repository->getProfesorTipos($aWhere, $aOperators);
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ProfesorTipo $ProfesorTipo): bool
    {
        return $this->repository->Eliminar($ProfesorTipo);
    }

    public function Guardar(ProfesorTipo $ProfesorTipo): bool
    {
        return $this->repository->Guardar($ProfesorTipo);
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
     * @param int $id_tipo_profesor
     * @return array|bool
     */
    public function datosById(int $id_tipo_profesor): array|bool
    {
        return $this->repository->datosById($id_tipo_profesor);
    }

    /**
     * Busca la clase con id_tipo_profesor en el repositorio.
     */
    public function findById(int $id_tipo_profesor): ?ProfesorTipo
    {
        return $this->repository->findById($id_tipo_profesor);
    }

    public function getNewId()
    {
        return $this->repository->getNewId();
    }
}