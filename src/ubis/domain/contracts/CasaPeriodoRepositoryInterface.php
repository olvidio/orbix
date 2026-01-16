<?php

namespace src\ubis\domain\contracts;

use PDO;
use src\ubis\domain\entity\CasaPeriodo;
use web\DateTimeLocal;


/**
 * Interfaz de la clase CasaPeriodo y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 29/12/2025
 */
interface CasaPeriodoRepositoryInterface
{
    public function getArrayCasaPeriodos(int $id_ubi, DateTimeLocal $oInicio, DateTimeLocal $oFin): array;

    public function getCasaPeriodosDias(int $iseccion, int $id_ubi, DateTimeLocal $oInicio, DateTimeLocal $oFin): int;
    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo CasaPeriodo
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo CasaPeriodo
     */
    public function getCasaPeriodos(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(CasaPeriodo $CasaPeriodo): bool;

    public function Guardar(CasaPeriodo $CasaPeriodo): bool;

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
    public function findById(int $id_item): ?CasaPeriodo;

    public function getNewId();
}