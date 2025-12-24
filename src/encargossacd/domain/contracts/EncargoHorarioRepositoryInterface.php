<?php

namespace src\encargossacd\domain\contracts;

use PDO;
use src\encargossacd\domain\entity\EncargoHorario;


/**
 * Interfaz de la clase EncargoHorario y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 23/12/2025
 */
interface EncargoHorarioRepositoryInterface
{

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo EncargoHorario
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo EncargoHorario
     */
    public function getEncargoHorarios(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(EncargoHorario $EncargoHorario): bool;

    public function Guardar(EncargoHorario $EncargoHorario): bool;

    public function getErrorTxt(): string;

    public function getoDbl(): PDO;

    public function setoDbl(PDO $oDbl): void;

    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_item_h
     * @return array|bool
     */
    public function datosById(int $id_item_h): array|bool;

    /**
     * Busca la clase con id_item_h en el repositorio.
     */
    public function findById(int $id_item_h): ?EncargoHorario;

    public function getNewId();
}