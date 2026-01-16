<?php

namespace src\procesos\domain\contracts;

use PDO;
use src\procesos\domain\entity\ActividadProcesoTarea;


/**
 * Interfaz de la clase ActividadProcesoTarea y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 26/12/2025
 */
interface ActividadProcesoTareaRepositoryInterface
{

    public function addFaseTarea(int $id_tipo_proceso, int $id_fase, int $id_tarea): void;

    public function getListaFaseEstado(int $iid_activ): array;
    public function getSacdAprobado(int $iid_activ): ?bool;


    public function getFasesCompletadas(int $iid_activ): array;
    public function faseCompletada(int $iid_activ, int $iid_fase): bool;



    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ActividadProcesoTarea
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo ActividadProcesoTarea
     */
    public function getActividadProcesoTareas(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ActividadProcesoTarea $ActividadProcesoTarea): bool;

    public function Guardar(ActividadProcesoTarea $ActividadProcesoTarea): bool;

    public function DBMarcar(ActividadProcesoTarea $ActividadProcesoTarea): bool;

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
    public function findById(int $id_item): ?ActividadProcesoTarea;

    public function getNewId();
}