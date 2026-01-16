<?php

namespace src\actividadplazas\domain\contracts;

use JsonException;
use PDO;
use src\actividadplazas\domain\entity\ActividadPlazas;


/**
 * Interfaz de la clase ActividadPlazas y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 16/12/2025
 */
interface ActividadPlazasRepositoryInterface
{

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ActividadPlazas
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo ActividadPlazas
     * @throws JsonException
     */
    public function getActividadesPlazas(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ActividadPlazas $ActividadPlazas): bool;

    public function Guardar(ActividadPlazas $ActividadPlazas): bool;

    public function getErrorTxt(): string;

    public function getoDbl(): PDO;

    public function setoDbl(PDO $oDbl): void;

    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_activ
     * @return array|bool
     * @throws JsonException
     */
    public function datosById(int $id_activ): array|bool;

    /**
     * Busca la clase con id_activ en el repositorio.
     * @throws JsonException
     */
    public function findById(int $id_activ): ?ActividadPlazas;
}