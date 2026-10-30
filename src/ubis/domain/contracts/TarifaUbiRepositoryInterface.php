<?php

namespace src\ubis\domain\contracts;

use src\ubis\domain\entity\TarifaUbi;
use src\shared\domain\criteria\Criteria;


/**
 * Interfaz de la clase TarifaUbi y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 29/12/2025
 */
interface TarifaUbiRepositoryInterface
{

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * TarifaUbi que cumplen el criterio.
     *
     * @return list<TarifaUbi>
     */
    public function matching(Criteria $criteria): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(TarifaUbi $TarifaUbi): bool;

    public function Guardar(TarifaUbi $TarifaUbi): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_item
     * @return array<string, mixed>|false
     */
    /**
     * @return array<string, mixed>|false
     */
    /**
     * @return array<string, mixed>|false
     */
    /**
     * @return array<string, mixed>|false
     */
    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_item): array|false;

    /**
     * Busca la clase con id_ubi en el repositorio.
     */
    public function findById(int $id_item): ?TarifaUbi;

    public function getNewId(): int;
}