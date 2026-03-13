<?php

namespace src\ubiscamas\domain\contracts;

use src\ubiscamas\domain\entity\Habitacion;

/**
 * Interfaz de la clase Habitacion y su Repositorio
 *
 * @package orbix
 * @subpackage ubiscamas
 * @author Daniel Serrabou
 * @version 1.0
 * @created 12/03/2026
 */
interface HabitacionRepositoryInterface
{

    public function getArrayHabitaciones($sCondicion = ''): array;

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Habitacion
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo Habitacion
     */
    public function getHabitaciones(array $aWhere = [], array $aOperators = []): array|false;

    /**
     * devuelve una colección (array) de objetos de tipo Habitacion para un id_ubi específico
     *
     * @param int $id_ubi
     * @return array|false Una colección de objetos de tipo Habitacion
     */
    public function getHabitacionesByUbi(int $id_ubi): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Habitacion $Habitacion): bool;

    public function Guardar(Habitacion $Habitacion): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param string $id_habitacion
     * @return array|bool
     */
    public function datosById(string $id_habitacion): array|bool;

    /**
     * Busca la clase con id_habitacion en el repositorio.
     */
    public function findById(string $id_habitacion): ?Habitacion;

    public function getNewId();
}
