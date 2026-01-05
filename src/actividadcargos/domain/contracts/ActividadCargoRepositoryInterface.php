<?php

namespace src\actividadcargos\domain\contracts;

use PDO;
use src\actividadcargos\domain\entity\ActividadCargo;


/**
 * Interfaz de la clase ActividadCargo y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 18/12/2025
 */
interface ActividadCargoRepositoryInterface
{

    public function getActividadIdSacds(int $iid_activ): array;

    public function getActividadSacds(int $iid_activ): array;

    public function getActividadCargosDeAsistente(array $aWhereNom, $aWhere = [], $aOperators = []): array;

    public function getAsistenteCargoDeActividad(array $aWhere, $aOperador = [], $aWhereAct = [], $aOperadorAct = []): array|false;

    public function getCargoDeActividad(array $aWhere, $aOperador = [], $aWhereAct = [], $aOperadorAct = []): array|false;


    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ActividadCargo
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo ActividadCargo
     */
    public function getActividadCargos(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ActividadCargo $ActividadCargo): bool;

    public function Guardar(ActividadCargo $ActividadCargo): bool;

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
    public function findById(int $id_item): ?ActividadCargo;

    public function getNewId();
}