<?php

namespace src\menus\domain\contracts;

use PDO;
use src\menus\domain\entity\MetaMenu;

/**
 * Interfaz de la clase MetaMenu y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 15/4/2025
 */
interface MetaMenuRepositoryInterface
{

    public function getArrayMetaMenus(array $a_modulos = []): array;

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo MetaMenu
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo MetaMenu
     */
    public function getMetaMenus(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(MetaMenu $MetaMenu): bool;

    public function Guardar(MetaMenu $MetaMenu): bool;

    public function getErrorTxt(): string;

    public function getoDbl(): PDO;

    public function setoDbl(PDO $oDbl): void;

    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_metamenu
     * @return array|bool
     */
    public function datosById(int $id_metamenu): array|bool;

    /**
     * Busca la clase con id_metamenu en el repositorio.
     */
    public function findById(int $id_metamenu): ?MetaMenu;

    public function getNewId();
}