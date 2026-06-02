<?php

namespace src\casas\domain\contracts;

use src\casas\domain\entity\UbiGasto;
use src\shared\domain\value_objects\DateTimeLocal;


/**
 * Interfaz de la clase UbiGasto y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 22/12/2025
 */
interface UbiGastoRepositoryInterface
{

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo UbiGasto
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array Una colección de objetos de tipo UbiGasto
     */
    public function getUbisGastos(array $aWhere = [], array $aOperators = []): array;

    /**
     * devuelve el sumatorio de gastos (o aportaciones) de una ubicación entre dos fechas para un tipo dado
     *
     * @param int $id_ubi id de la ubicación (casa)
     * @param int $tipo 1: aportación de asistentes, 2: aportación de centros, 3: gasto
     * @param DateTimeLocal $oInicio fecha de inicio (incluida)
     * @param DateTimeLocal $oFin fecha de fin (incluida)
     * @return float suma total del tipo solicitado
     */
    public function getSumaGastos(int $id_ubi, int $tipo, DateTimeLocal $oInicio, DateTimeLocal $oFin): float;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(UbiGasto $UbiGasto): bool;

    public function Guardar(UbiGasto $UbiGasto): bool;

    public function getErrorTxt(): string;



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
    public function findById(int $id_item): ?UbiGasto;


    public function getNewId();
}