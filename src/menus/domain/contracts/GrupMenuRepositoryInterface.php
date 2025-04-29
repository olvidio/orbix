<?php

namespace src\menus\domain\contracts;

use PDO;
use src\menus\domain\entity\GrupMenu;


/**
 * Interfaz de la clase GrupMenu y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 15/4/2025
 */
interface GrupMenuRepositoryInterface
{

    public function getArrayGrupMenus(): array;

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo GrupMenu
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo GrupMenu
     */
    public function getGrupMenus(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(GrupMenu $GrupMenu): bool;

    public function Guardar(GrupMenu $GrupMenu): bool;

    public function getErrorTxt(): string;

    public function getoDbl(): PDO;

    public function setoDbl(PDO $oDbl): void;

    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_grupmenu
     * @return array|bool
     */
    public function datosById(int $id_grupmenu): array|bool;

    /**
     * Busca la clase con id_grupmenu en el repositorio.
     */
    public function findById(int $id_grupmenu): ?GrupMenu;

    public function getNewId();
}