<?php

namespace src\ubis\domain\contracts;

use PDO;
use src\ubis\domain\entity\TelecoUbi;

/**
 * Interfaz de la clase TelecoCdc y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 19/11/2025
 */
interface TelecoUbiRepositoryInterface
{

    /* -------------------- GESTOR BASE ---------------------------------------- */

    public function getTelecos(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(TelecoUbi $TelecoCdc): bool;

    public function Guardar(TelecoUbi $TelecoCdc): bool;

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
    public function findById(int $id_item): ?TelecoUbi;

    public function getNewId();
}