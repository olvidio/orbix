<?php

namespace src\usuarios\application\repositories;

use PDO;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
use src\usuarios\domain\entity\Preferencia;
use src\usuarios\infrastructure\repositories\PgPreferenciaRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo Preferencia
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/4/2025
 */
class PreferenciaRepository implements PreferenciaRepositoryInterface
{

    /**$
     * @var PreferenciaRepositoryInterface
     */
    private PreferenciaRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgPreferenciaRepository();
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Preferencia
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo Preferencia
     */
    public function getPreferencias(array $aWhere = [], array $aOperators = []): array|false
    {
        return $this->repository->getPreferencias($aWhere, $aOperators);
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Preferencia $Preferencia): bool
    {
        return $this->repository->Eliminar($Preferencia);
    }

    public function Guardar(Preferencia $Preferencia): bool
    {
        return $this->repository->Guardar($Preferencia);
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
     * @param string $tipo
     * @return array|bool
     */
    public function datosById(int $id_usuario, string $tipo): array|bool
    {
        return $this->repository->datosById($id_usuario, $tipo);
    }

    /**
     * Busca la clase con tipo en el repositorio.
     */
    public function findById(int $id_usuario, string $tipo): ?Preferencia
    {
        return $this->repository->findById($id_usuario, $tipo);
    }
}