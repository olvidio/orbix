<?php

namespace src\personas\domain\contracts;

use src\personas\domain\entity\TelecoPersona;


/**
 * Interfaz de la clase TelecoPersona y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/12/2025
 */
interface TelecoPersonaDlRepositoryInterface extends TelecoPersonaRepositoryInterface
{

    public function getNewId();

}