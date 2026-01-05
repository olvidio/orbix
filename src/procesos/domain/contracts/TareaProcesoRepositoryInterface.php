<?php

namespace src\procesos\domain\contracts;

use JsonException;
use PDO;
use src\procesos\domain\entity\TareaProceso;


/**
 * Interfaz de la clase TareaProceso y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 26/12/2025
 */
interface TareaProcesoRepositoryInterface
{

    public function getArrayFasesDependientes(int $iid_tipo_proceso): array;

    public function arbolPrevio(int $iid_tipo_proceso): array;

    public function getStatusProceso(int $iid_tipo_proceso, array $aFasesEstado): int;

    public function getFasesProceso(int $iid_tipo_proceso): array;

    public function getFaseIndependiente(int $id_tipo_proceso): array;


    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo TareaProceso
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo TareaProceso
     * @throws JsonException
     */
    public function getTareasProceso(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(TareaProceso $TareaProceso): bool;

    public function Guardar(TareaProceso $TareaProceso): bool;

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
     * @throws JsonException
     */
    public function datosById(int $id_item): array|bool;

    /**
     * Busca la clase con id_item en el repositorio.
     * @throws JsonException
     */
    public function findById(int $id_item): ?TareaProceso;

    public function getNewId();
}