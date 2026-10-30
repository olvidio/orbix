<?php

namespace src\personas\domain\contracts;

use src\shared\domain\criteria\Criteria;
use src\personas\domain\entity\PersonaDl;


/**
 * Interfaz de la clase PersonaDl y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 9/12/2025
 */
interface PersonaDlRepositoryInterface
{

    /**
     * Devuelve un array con los id de centros (id_ctr) de personas activas.
     *
     * @param string $sdonde condición extra SQL (debe empezar por AND)
     * @return array<int|string, int|string>
     */
    public function getArrayIdCentros(string $sdonde = ''): array;

    /**
     * Lista de posibles SACD en array [id_nom => ape_nom].
     *
     * @param string $sdonde condición extra SQL (debe empezar por AND)
     * @return array<int|string, string>
     */
    public function getArraySacd(string $sdonde = ''): array;

    /**
     * Lista de personas activas en array [id_nom => ape_nom(centro)].
     *
     * @return array<int|string, string>
     */
    public function getArrayPersonas(string $id_tabla = ''): array;


    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * @return list<PersonaDl>
     */
    public function matching(Criteria $criteria): array;

    /**
     * Busca la clase con id_nom en el repositorio.
     */
    public function findById(int $id_nom): ?PersonaDl;
}
