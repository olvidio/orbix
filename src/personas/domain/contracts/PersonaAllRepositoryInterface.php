<?php

namespace src\personas\domain\contracts;


/**
 * Interfaz de la clase PersonaN y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 9/12/2025
 */
interface PersonaAllRepositoryInterface
{
    public function getPersonaByIdNom($id_nom);
}