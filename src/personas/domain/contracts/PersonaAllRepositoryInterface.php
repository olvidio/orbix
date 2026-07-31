<?php

namespace src\personas\domain\contracts;

use src\personas\domain\entity\PersonaPub;


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
     * Resolución histórica por id_nom (caso A): no exige publicación vigente.
     * Mismo orden de preferencia que v_personas_pub + desempate f_situacion / id_schema.
     *
     * @param int|null $id_schema Si se conoce, restringe a ese esquema.
     */
    public function findByIdNomParaLookup(int $id_nom, ?int $id_schema = null): ?PersonaPub;

    /**
     * Publica la persona para una DL destino (o "*") con TTL por DL.
     * Une el mapa existente y no acorta la caducidad de esa DL.
     *
     * @param string $dlOrStar Código dl (p.ej. dlb) o PersonaPublicacion::DL_TODAS
     * @param \DateTimeInterface|null $hasta null = sin caducidad ("*") o default TTL si $dlOrStar !== '*'
     */
    public function marcarPublicadoPara(
        int $id_nom,
        int $id_schema,
        string $dlOrStar,
        ?\DateTimeInterface $hasta = null,
    ): bool;
}
