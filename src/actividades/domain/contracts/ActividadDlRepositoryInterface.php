<?php

namespace src\actividades\domain\contracts;


/**
 * Interfaz de la clase ActividadDl y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 3/12/2025
 */
interface ActividadDlRepositoryInterface extends ActividadAllRepositoryInterface
{

    public function getNewId(): int;

    public function getNewIdActividad(int $id): int;

    public function deleteActividadesEnPeriodoEnProyecto(string $f_ini, string $f_fin): bool;

    /**
     * @return array<int, string>
     */
    public function getArrayActividadesEnPeriodoNoEnProyecto(string $f_ini, string $f_fin): array;

    /**
     * Ejecuta SQL de mantenimiento en la conexión de escritura del esquema común (p. ej. limpieza de huérfanos).
     */
    public function execMaintenanceSql(string $sql): bool;
}
