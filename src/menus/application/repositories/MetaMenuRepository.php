<?php

namespace src\menus\application\repositories;

use PDO;
use src\menus\domain\entity\MetaMenu;
use src\menus\domain\contracts\MetaMenuRepositoryInterface;
use src\menus\infrastructure\repositories\PgMetaMenuRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo MetaMenu
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 15/4/2025
 */
class MetaMenuRepository implements MetaMenuRepositoryInterface
{

    /**$
     * @var MetaMenuRepositoryInterface
     */
    private MetaMenuRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgMetaMenuRepository();
    }

    public function getArrayMetaMenus(array $a_modulos=[]): array
    {
       return $this->repository->getArrayMetaMenus($a_modulos);
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo MetaMenu
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo MetaMenu
     */
    public function getMetaMenus(array $aWhere = [], array $aOperators = []): array|false
    {
        return $this->repository->getMetaMenus($aWhere, $aOperators);
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(MetaMenu $MetaMenu): bool
    {
        return $this->repository->Eliminar($MetaMenu);
    }

    public function Guardar(MetaMenu $MetaMenu): bool
    {
        return $this->repository->Guardar($MetaMenu);
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
     * @param int $id_metamenu
     * @return array|bool
     */
    public function datosById(int $id_metamenu): array|bool
    {
        return $this->repository->datosById($id_metamenu);
    }

    /**
     * Busca la clase con id_metamenu en el repositorio.
     */
    public function findById(int $id_metamenu): ?MetaMenu
    {
        return $this->repository->findById($id_metamenu);
    }

    public function getNewId()
    {
        return $this->repository->getNewId();
    }
}