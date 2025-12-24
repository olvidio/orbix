<?php

namespace src\personas\domain\contracts;

use src\personas\domain\entity\PersonaN;


/**
 * Interfaz de la clase PersonaN y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 9/12/2025
 */
interface PersonaNRepositoryInterface extends PersonaDlRepositoryInterface
{

    /**
     * Busca la clase con id_nom en el repositorio.
     */
    public function findById(int $id_nom): ?PersonaN;

    public function getNewId();

    public function getNewIdNom($id): int;
}