<?php

namespace src\ubis\domain\contracts;

use PDO;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\entity\Direccion;


/**
 * Interfaz de la clase CentroDl y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/11/2025
 */
interface CentroDlRepositoryInterface
{

    public function getArrayCentros($sCondicion = ''): array;

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo CentroDl
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo CentroDl
     */
    public function getCentros(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(CentroDl $CentroDl): bool;

    public function Guardar(CentroDl $CentroDl): bool;

    public function getErrorTxt(): string;

    public function getoDbl(): PDO;

    public function setoDbl(PDO $oDbl): void;

    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_ubi
     * @return array|bool
     */
    public function datosById(int $id_ubi): array|bool;

    /**
     * Busca la clase con id_ubi en el repositorio.
     */
    public function findById(int $id_ubi): ?CentroDl;

    public function getNewId();

    public function getNewIdUbi($id): int;
}