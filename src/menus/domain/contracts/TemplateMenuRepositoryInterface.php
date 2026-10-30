<?php

namespace src\menus\domain\contracts;

use src\menus\domain\entity\TemplateMenu;
use src\shared\domain\criteria\Criteria;

/**
 * Interfaz de la clase TemplateMenu y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 15/4/2025
 */
interface TemplateMenuRepositoryInterface
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
    public function getArrayTemplates(): array;

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * Plantillas de menú que cumplen el criterio.
     *
     * @return list<TemplateMenu>
     */
    public function matching(Criteria $criteria): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(TemplateMenu $TemplateMenu): bool;

    public function Guardar(TemplateMenu $TemplateMenu): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_template_menu
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_template_menu): array|false;

    /**
     * Busca la clase con id_template_menu en el repositorio.
     */
    public function findById(int $id_template_menu): ?TemplateMenu;

    public function findByName(string $nombre): ?TemplateMenu;

    public function getNewId(): int;

}