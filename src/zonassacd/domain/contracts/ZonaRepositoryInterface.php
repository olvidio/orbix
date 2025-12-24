<?php

namespace src\zonassacd\domain\contracts;

use PDO;
use src\zonassacd\domain\entity\Zona;


/**
 * Interfaz de la clase Zona y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/12/2025
 */
interface ZonaRepositoryInterface
{


    public function isJefeZona(int $id_nom): bool;

    public function getArrayZonas(?int $iid_nom_jefe = null): array;


    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Zona
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo Zona
     */
    public function getZonas(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Zona $Zona): bool;

    public function Guardar(Zona $Zona): bool;

    public function getErrorTxt(): string;

    public function getoDbl(): PDO;

    public function setoDbl(PDO $oDbl): void;

    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_zona
     * @return array|bool
     */
    public function datosById(int $id_zona): array|bool;

    /**
     * Busca la clase con id_zona en el repositorio.
     */
    public function findById(int $id_zona): ?Zona;

    public function getNewId();
}