<?php

namespace src\usuarios\domain\contracts;

use PDO;
use src\usuarios\domain\entity\Grupo;

/**
 * Interfaz de la clase Grupo y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/4/2025
 */
interface GrupoRepositoryInterface
{

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Grupo
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo Grupo
     */
    public function getGrupos(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Grupo $Grupo): bool;

    public function Guardar(Grupo $Grupo): bool;

    public function getErrorTxt(): string;

    public function getoDbl(): PDO;

    public function setoDbl(PDO $oDbl): void;

    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_usuario
     * @return array|bool
     */
    public function datosById(int $id_usuario): array|bool;

    /**
     * Busca la clase con id_usuario en el repositorio.
     */
    public function findById(int $id_usuario): ?Grupo;

    public function getNewId();
}