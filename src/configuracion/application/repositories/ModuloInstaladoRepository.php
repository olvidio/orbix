<?php

namespace src\configuracion\application\repositories;

use PDO;
use src\configuracion\domain\contracts\ModuloInstaladoRepositoryInterface;
use src\configuracion\domain\entity\ModuloInstalado;
use src\configuracion\infrastructure\repositories\PgModuloInstaladoRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo ModuloInstalado
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 13/11/2025
 */
class ModuloInstaladoRepository implements ModuloInstaladoRepositoryInterface
{

    /**$
     * @var ModuloInstaladoRepositoryInterface
     */
    private ModuloInstaladoRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgModuloInstaladoRepository();
    }

    public function getArrayModulosInstalados(): array
    {
        return $this->repository->getArrayModulosInstalados();
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ModuloInstalado
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo ModuloInstalado
     */
    public function getModuloInstalados(array $aWhere = [], array $aOperators = []): array|false
    {
        return $this->repository->getModuloInstalados($aWhere, $aOperators);
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ModuloInstalado $ModuloInstalado): bool
    {
        return $this->repository->Eliminar($ModuloInstalado);
    }

    public function Guardar(ModuloInstalado $ModuloInstalado): bool
    {
        return $this->repository->Guardar($ModuloInstalado);
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
    public function findById(int $id_mod): ?ModuloInstalado
    {
        return $this->repository->findById($id_mod);
    }
}