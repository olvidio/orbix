<?php

namespace src\personas\domain\contracts;


/**
 * Interfaz de la clase TelecoPersona y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/12/2025
 */
interface TelecoPersonaExRepositoryInterface extends TelecoPersonaRepositoryInterface
{

    public function getNewId();

}