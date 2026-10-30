<?php

namespace src\actividadplazas\domain\contracts;

use src\actividadplazas\domain\entity\PlazaPeticion;
use src\actividadplazas\domain\value_objects\PlazaPeticionPk;
use src\shared\domain\criteria\Criteria;


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
     * PlazaPeticion que cumplen el criterio.
     *
     * @return list<PlazaPeticion>
     */
    public function matching(Criteria $criteria): array;

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