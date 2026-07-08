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
    public function getPersonaByIdNom(int $id_nom): ?\src\personas\domain\entity\PersonaDl;

    /**
     * Marca la fila activa de `global.personas` como visible en otras dl (`v_personas_pub`).
     */
    public function marcarEsPublico(int $id_nom, int $id_schema): bool;
}