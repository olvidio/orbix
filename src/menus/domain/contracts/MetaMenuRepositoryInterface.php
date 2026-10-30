<?php

namespace src\menus\domain\contracts;

use src\menus\domain\entity\MetaMenu;
use src\shared\domain\criteria\Criteria;

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

    /**
     * @param list<string> $a_modulos
     * @return array<int|string, string>
     */
    public function getArrayMetaMenus(array $a_modulos = []): array;

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * Metamenús que cumplen el criterio.
     *
     * @return list<MetaMenu>
     */
    public function matching(Criteria $criteria): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(MetaMenu $MetaMenu): bool;

    public function Guardar(MetaMenu $MetaMenu): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_metamenu
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_metamenu): array|false;

    /**
     * Busca la clase con id_metamenu en el repositorio.
     */
    public function findById(int $id_metamenu): ?MetaMenu;

    public function getNewId(): int;
}