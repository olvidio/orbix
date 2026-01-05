<?php

namespace src\procesos\domain\contracts;

use PDO;
use src\procesos\domain\entity\ActividadFase;


/**
 * Interfaz de la clase ActividadFase y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/12/2025
 */
interface ActividadFaseRepositoryInterface
{

    public function getTodasActividadFases(array $a_id_tipo_proceso): array;


    public function getArrayActividadFasesTodas(array $aProcesos): array;

    public function getArrayFasesProcesos(array $aProcesos = []): array;

    public function getArrayActividadFases(array $aProcesos = [], bool $bresp = false): array;

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ActividadFase
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo ActividadFase
     */
    public function getActividadFases(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ActividadFase $ActividadFase): bool;

    public function Guardar(ActividadFase $ActividadFase): bool;

    public function getErrorTxt(): string;

    public function getoDbl(): PDO;

    public function setoDbl(PDO $oDbl): void;

    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_fase
     * @return array|bool
     */
    public function datosById(int $id_fase): array|bool;

    /**
     * Busca la clase con id_fase en el repositorio.
     */
    public function findById(int $id_fase): ?ActividadFase;

    public function getNewId();
}