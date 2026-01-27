<?php

namespace src\personas\domain\contracts;

use src\personas\domain\entity\PersonaS;


/**
 * Interfaz de la clase PersonaS y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 9/12/2025
 */
interface PersonaSRepositoryInterface extends PersonaDlRepositoryInterface
{
    public function Guardar(PersonaS $PersonaS): bool;

    public function Eliminar(PersonaS $PersonaS): bool;

    /**
     * Busca la clase con id_nom en el repositorio.
     */
    public function findById(int $id_nom): ?PersonaS;

    public function getNewId();

    public function getNewIdNom($id): int;
}