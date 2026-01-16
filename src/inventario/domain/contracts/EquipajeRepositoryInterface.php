<?php

namespace src\inventario\domain\contracts;

use PDO;
use src\inventario\domain\entity\Equipaje;

/**
 * Interfaz de la clase Equipaje y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
interface EquipajeRepositoryInterface
{

    public function getEquipajesCoincidentes(string $f_ini_iso, string $f_fin_iso): array|false;

    public function getArrayEquipajes(string $f_ini_iso = ''): array|false;

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Equipaje
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo Equipaje
     */
    public function getEquipajes(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Equipaje $Equipaje): bool;

    public function Guardar(Equipaje $Equipaje): bool;

    public function getErrorTxt(): string;

    public function getoDbl(): PDO;

    public function setoDbl(PDO $oDbl): void;

    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_equipaje
     * @return array|bool
     */
    public function datosById(int $id_equipaje): array|bool;

    /**
     * Busca la clase con id_equipaje en el repositorio.
     */
    public function findById(int $id_equipaje): ?Equipaje;

    public function getNewId();
}