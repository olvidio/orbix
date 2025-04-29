<?php

namespace src\usuarios\application\repositories;

use PDO;
use src\usuarios\domain\contracts\LocalRepositoryInterface;
use src\usuarios\domain\entity\Local;
use src\usuarios\infrastructure\repositories\PgLocalRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo Local
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/4/2025
 */
class LocalRepository implements LocalRepositoryInterface
{

    /**$
     * @var LocalRepositoryInterface
     */
    private LocalRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgLocalRepository();
    }

    public function getArrayLocales(): array
    {
        return $this->repository->getArrayLocales();
    }
    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Local
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo Local
     */
    public function getLocales(array $aWhere = [], array $aOperators = []): array|false
    {
        return $this->repository->getLocales($aWhere, $aOperators);
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Local $Local): bool
    {
        return $this->repository->Eliminar($Local);
    }

    public function Guardar(Local $Local): bool
    {
        return $this->repository->Guardar($Local);
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
     * @param string $id_locale
     * @return array|bool
     */
    public function datosById(string $id_locale): array|bool
    {
        return $this->repository->datosById($id_locale);
    }

    /**
     * Busca la clase con id_locale en el repositorio.
     */
    public function findById(string $id_locale): ?Local
    {
        return $this->repository->findById($id_locale);
    }
}