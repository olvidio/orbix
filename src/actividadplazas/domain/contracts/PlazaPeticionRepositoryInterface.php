<?php

namespace src\actividadplazas\domain\contracts;

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
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<PlazaPeticion>
     */
    public function getPlazasPeticion(array $aWhere = [], array $aOperators = []): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(PlazaPeticion $PlazaPeticion): bool;

    public function Guardar(PlazaPeticion $PlazaPeticion): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_nom, int $id_activ): array|false;

    /**
     * @return array<string, mixed>|false
     */
    public function datosByPk(PlazaPeticionPk $pk): array|false;

    public function findById(int $id_nom, int $id_activ): ?PlazaPeticion;

    public function findByPk(PlazaPeticionPk $pk): ?PlazaPeticion;
}