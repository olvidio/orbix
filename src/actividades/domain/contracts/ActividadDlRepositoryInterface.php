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

    public function deleteActividadesEnPeriodoEnProyecto($f_ini, $f_fin): bool;

    public function getArrayActividadesEnPeriodoNoEnProyecto($f_ini, $f_fin): array;
}