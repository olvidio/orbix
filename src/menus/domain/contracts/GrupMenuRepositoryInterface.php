<?php

namespace src\menus\domain\contracts;

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

    /**
     * @return array<int|string, string>
     */
    /**
     * @return array<int|string, string>
     */
    /**
     * @return array<int|string, string>
     */
    public function getArrayGrupMenus(): array;

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo GrupMenu
     *
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<GrupMenu> Una colección de objetos de tipo GrupMenu
     */
    public function getGrupMenus(array $aWhere = [], array $aOperators = []): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(GrupMenu $GrupMenu): bool;

    public function Guardar(GrupMenu $GrupMenu): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_grupmenu
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_grupmenu): array|false;

    /**
     * Busca la clase con id_grupmenu en el repositorio.
     */
    public function findById(int $id_grupmenu): ?GrupMenu;

    public function getNewId(): int;
}