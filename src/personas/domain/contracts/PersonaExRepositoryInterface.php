<?php

namespace src\personas\domain\contracts;



use src\personas\domain\entity\PersonaEx;

/**
 * Interfaz de la clase PersonaN y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 9/12/2025
 */
interface PersonaExRepositoryInterface extends PersonaPubRepositoryInterface
{

    /**
     * Busca la clase con id_nom en el repositorio.
     */
    public function findById(int $id_nom): ?PersonaEx;

    public function getNewId();

    public function getNewIdNom($id): int;
}