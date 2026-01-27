<?php

namespace src\personas\domain\contracts;

use src\personas\domain\entity\PersonaAgd;
use src\personas\domain\entity\PersonaDl;


/**
 * Interfaz de la clase PersonaAgd y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 9/12/2025
 */
interface PersonaAgdRepositoryInterface extends PersonaDlRepositoryInterface
{
    public function Guardar(PersonaAgd $PersonaAgd): bool;

    public function Eliminar(PersonaAgd $PersonaAgd): bool;

    /**
     * Busca la clase con id_nom en el repositorio.
     */
    public function findById(int $id_nom): ?PersonaAgd;

    public function getNewId();

    public function getNewIdNom($id): int;

}