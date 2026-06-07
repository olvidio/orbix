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

    /**
     * @return array<int|string, string>
     */
    /**
     * @return array<int|string, string>
     */
    public function getArrayHabitaciones(string $sCondicion = ''): array;

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Habitacion
     *
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<Habitacion> Una colección de objetos de tipo Habitacion
     */
    public function getHabitaciones(array $aWhere = [], array $aOperators = []): array;

    /**
     * devuelve una colección (array) de objetos de tipo Habitacion para un id_ubi específico
     *
     * @param int $id_ubi
     * @return list<Habitacion> Una colección de objetos de tipo Habitacion
     */
    public function getHabitacionesByUbi(int $id_ubi): array;

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
     * @return array<string, mixed>|false
     */
    public function datosById(string $id_habitacion): array|false;

    /**
     * Busca la clase con id_habitacion en el repositorio.
     */
    public function findById(string $id_habitacion): ?Habitacion;

    public function getNewId(): string|false;
}
