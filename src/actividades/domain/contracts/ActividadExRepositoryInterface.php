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
interface ActividadExRepositoryInterface extends ActividadAllRepositoryInterface
{

    public function getNewId():int;

    public function getNewIdActividad(int $id): int;
}