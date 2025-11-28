<?php

namespace src\ubis\domain\contracts;

/**
 * Interfaz de la clase Direccion y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 21/11/2025
 */
interface DireccionCentroDlRepositoryInterface extends DireccionRepositoryInterface
{
    public function getNewId(): int;
    public function getNewIdDireccion($id): int;
}