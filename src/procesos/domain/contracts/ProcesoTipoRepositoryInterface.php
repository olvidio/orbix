<?php

namespace src\procesos\domain\contracts;

use PDO;
use src\procesos\domain\entity\ProcesoTipo;


/**
 * Interfaz de la clase ProcesoTipo y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 26/12/2025
 */
interface ProcesoTipoRepositoryInterface
{

    public function getArrayProcesoTipos(): array;

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ProcesoTipo
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo ProcesoTipo
     */
    public function getProcesoTipos(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ProcesoTipo $ProcesoTipo): bool;

    public function Guardar(ProcesoTipo $ProcesoTipo): bool;

    public function getErrorTxt(): string;

    public function getoDbl(): PDO;

    public function setoDbl(PDO $oDbl): void;

    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_tipo_proceso
     * @return array|bool
     */
    public function datosById(int $id_tipo_proceso): array|bool;

    /**
     * Busca la clase con id_tipo_proceso en el repositorio.
     */
    public function findById(int $id_tipo_proceso): ?ProcesoTipo;

    public function getNewId();
}