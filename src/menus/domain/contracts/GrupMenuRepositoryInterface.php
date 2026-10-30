<?php

namespace src\menus\domain\contracts;

use src\menus\domain\entity\GrupMenu;
use src\shared\domain\criteria\Criteria;

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
     * Grupos de menú que cumplen el criterio.
     *
     * @return list<GrupMenu>
     */
    public function matching(Criteria $criteria): array;

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