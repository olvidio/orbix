<?php

namespace src\cambios\domain\contracts;

use JsonException;
use PDO;
use src\cambios\domain\entity\Cambio;


/**
 * Interfaz de la clase CambioDl y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 19/12/2025
 */
interface CambioRepositoryInterface
{

    public function getNomActivEliminada($iId): string;

    public function borrarCambios(string $str_interval = 'P1Y'): void;

    public function getCambiosNuevos(): array;

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo CambioDl
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo CambioDl
     * @throws JsonException
     */
    public function getCambios(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Cambio $Cambio): bool;

    public function Guardar(Cambio $Cambio): bool;

    public function getErrorTxt(): string;

    public function getoDbl(): PDO;

    public function setoDbl(PDO $oDbl): void;

    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_item_cambio
     * @return array|bool
     * @throws JsonException
     */
    public function datosById(int $id_item_cambio): array|bool;

    /**
     * Busca la clase con id_item_cambio en el repositorio.
     * @throws JsonException
     */
    public function findById(int $id_item_cambio): ?Cambio;

    public function getNewId();
}