<?php

namespace src\ubiscamas\domain\contracts;

use src\ubiscamas\domain\entity\Cama;

/**
 * Interfaz de la clase Cama y su Repositorio
 *
 * @package orbix
 * @subpackage ubiscamas
 * @author Daniel Serrabou
 * @version 1.0
 * @created 12/03/2026
 */
interface CamaRepositoryInterface
{

    public function getArrayCamas($sCondicion = ''): array;

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Cama
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo Cama
     */
    public function getCamas(array $aWhere = [], array $aOperators = []): array|false;

    /**
     * devuelve una colección (array) de objetos de tipo Cama para una habitación específica
     *
     * @param int $id_habitacion
     * @return array|false Una colección de objetos de tipo Cama
     */
    public function getCamasByHabitacion(int $id_habitacion): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Cama $Cama): bool;

    public function Guardar(Cama $Cama): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param string $id_cama
     * @return array|bool
     */
    public function datosById(string $id_cama): array|bool;

    /**
     * Busca la clase con id_cama en el repositorio.
     */
    public function findById(string $id_cama): ?Cama;

    public function getNewId();
}
