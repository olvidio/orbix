<?php

namespace src\menus\domain\contracts;

use src\menus\domain\entity\MenuDb;
use src\shared\domain\criteria\Criteria;

/**
 * Interfaz de la clase MenuDb y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 15/4/2025
 */
interface MenuDbRepositoryInterface
{

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * Menús que cumplen el criterio.
     *
     * @return list<MenuDb>
     */
    public function matching(Criteria $criteria): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(MenuDb $MenuDb): bool;

    public function Guardar(MenuDb $MenuDb): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_menu
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_menu): array|false;

    /**
     * Busca la clase con id_menu en el repositorio.
     */
    public function findById(int $id_menu): ?MenuDb;

    public function getNewId(): int;
}