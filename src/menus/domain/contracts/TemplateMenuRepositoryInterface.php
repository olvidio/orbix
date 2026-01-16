<?php

namespace src\menus\domain\contracts;

use PDO;
use src\menus\domain\entity\TemplateMenu;

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

    public function getArrayTemplates(): array;

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo TemplateMenu
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo TemplateMenu
     */
    public function getTemplatesMenus(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(TemplateMenu $TemplateMenu): bool;

    public function Guardar(TemplateMenu $TemplateMenu): bool;

    public function getErrorTxt(): string;

    public function getoDbl(): PDO;

    public function setoDbl(PDO $oDbl): void;

    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_template_menu
     * @return array|bool
     */
    public function datosById(int $id_template_menu): array|bool;

    /**
     * Busca la clase con id_template_menu en el repositorio.
     */
    public function findById(int $id_template_menu): ?TemplateMenu;

    public function findByName(string $nombre): ?TemplateMenu;

    public function getNewId();

}