<?php

namespace src\zonassacd\domain\contracts;

use PDO;
use src\zonassacd\domain\entity\ZonaSacd;


/**
 * Interfaz de la clase ZonaSacd y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/12/2025
 */
interface ZonaSacdRepositoryInterface
{

    public function getIdSacdsDeZona(int $iid_zona): array;

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ZonaSacd
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo ZonaSacd
     */
    public function getZonasSacds(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ZonaSacd $ZonaSacd): bool;

    public function Guardar(ZonaSacd $ZonaSacd): bool;

    public function getErrorTxt(): string;

    public function getoDbl(): PDO;

    public function setoDbl(PDO $oDbl): void;

    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_item
     * @return array|bool
     */
    public function datosById(int $id_item): array|bool;

    /**
     * Busca la clase con id_item en el repositorio.
     */
    public function findById(int $id_item): ?ZonaSacd;

    public function getNewId();
}