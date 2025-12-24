<?php

namespace src\zonassacd\domain\contracts;

use PDO;
use src\zonassacd\domain\entity\ZonaGrupo;


/**
 * Interfaz de la clase ZonaGrupo y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/12/2025
 */
interface ZonaGrupoRepositoryInterface
{

    public function getArrayZonaGrupos(string $sCondicion = ''): array;

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ZonaGrupo
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo ZonaGrupo
     */
    public function getZonasGrupo(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ZonaGrupo $ZonaGrupo): bool;

    public function Guardar(ZonaGrupo $ZonaGrupo): bool;

    public function getErrorTxt(): string;

    public function getoDbl(): PDO;

    public function setoDbl(PDO $oDbl): void;

    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_grupo
     * @return array|bool
     */
    public function datosById(int $id_grupo): array|bool;

    /**
     * Busca la clase con id_grupo en el repositorio.
     */
    public function findById(int $id_grupo): ?ZonaGrupo;

    public function getNewId();
}