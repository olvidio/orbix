<?php

namespace src\procesos\domain\contracts;

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

    /**
     * @param list<int> $a_id_tipo_proceso
     * @return list<int>
     */
    public function getTodasActividadFases(array $a_id_tipo_proceso): array;

    /**
     * @param list<int> $aProcesos
     * @return array<string, int>
     */
    public function getArrayActividadFasesTodas(array $aProcesos): array;

    /**
     * @param list<int> $aProcesos
     * @return array<int|string, int>
     */
    public function getArrayFasesProcesos(array $aProcesos = []): array;

    /**
     * @param list<int> $aProcesos
     * @return array<int|string, string>
     */
    public function getArrayActividadFases(array $aProcesos = [], bool $bresp = false): array;

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ActividadFase
     *
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<ActividadFase> Una colección de objetos de tipo ActividadFase
     */
    public function getActividadFases(array $aWhere = [], array $aOperators = []): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ActividadFase $ActividadFase): bool;

    public function Guardar(ActividadFase $ActividadFase): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_fase
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_fase): array|false;

    /**
     * Busca la clase con id_fase en el repositorio.
     */
    public function findById(int $id_fase): ?ActividadFase;

    public function getNewId(): int;
}