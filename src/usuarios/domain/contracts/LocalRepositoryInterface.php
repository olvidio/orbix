<?php

namespace src\usuarios\domain\contracts;

use PDO;
use src\usuarios\domain\entity\Local;

/**
 * Interfaz de la clase Local y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/4/2025
 */
interface LocalRepositoryInterface
{
    public function getArrayLocales(): array;
    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Local
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo Local
     */
    public function getLocales(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Local $Local): bool;

    public function Guardar(Local $Local): bool;

    public function getErrorTxt(): string;

    public function getoDbl(): PDO;

    public function setoDbl(PDO $oDbl): void;

    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param string $id_locale
     * @return array|bool
     */
    public function datosById(string $id_locale): array|bool;

    /**
     * Busca la clase con id_locale en el repositorio.
     */
    public function findById(string $id_locale): ?Local;
}