<?php

namespace src\config\application\repositories;

use PDO;
use src\config\domain\contracts\AppRepositoryInterface;
use src\config\domain\entity\App;
use src\config\infrastructure\repositories\PgAppRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo App
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 10/11/2025
 */
class AppRepository implements AppRepositoryInterface
{

    /**$
     * @var AppRepositoryInterface
     */
    private AppRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgAppRepository();
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo App
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo App
     */
    public function getApps(array $aWhere = [], array $aOperators = []): array|false
    {
        return $this->repository->getApps($aWhere, $aOperators);
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(App $App): bool
    {
        return $this->repository->Eliminar($App);
    }

    public function Guardar(App $App): bool
    {
        return $this->repository->Guardar($App);
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
     * @param int $id_app
     * @return array|bool
     */
    public function datosById(int $id_app): array|bool
    {
        return $this->repository->datosById($id_app);
    }

    /**
     * Busca la clase con id_app en el repositorio.
     */
    public function findById(int $id_app): ?App
    {
        return $this->repository->findById($id_app);
    }

    public function getNewId()
    {
        return $this->repository->getNewId();
    }
}