<?php

namespace src\actividadplazas\domain\contracts;

use PDO;
use src\actividadplazas\domain\entity\PlazaPeticion;
use src\actividadplazas\domain\value_objects\PlazaPeticionPk;


/**
 * Interfaz de la clase PlazaPeticion y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 29/12/2025
 */
interface PlazaPeticionRepositoryInterface
{

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo PlazaPeticion
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo PlazaPeticion
     */
    public function getPlazasPeticion(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(PlazaPeticion $PlazaPeticion): bool;

    public function Guardar(PlazaPeticion $PlazaPeticion): bool;

    public function getErrorTxt(): string;

    public function getoDbl(): PDO;

    public function setoDbl(PDO $oDbl): void;

    public function getNomTabla(): string;

    public function datosById(int $id_nom, int $id_activ): array|bool;

    public function datosByPk(PlazaPeticionPk $pk): array|bool;

    public function findById(int $id_nom, int $id_activ): ?PlazaPeticion;

    public function findByPk(PlazaPeticionPk $pk): ?PlazaPeticion;
}