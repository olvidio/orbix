<?php

namespace src\personas\domain\contracts;

use src\personas\domain\entity\PersonaSacd;


/**
 * Interfaz de la clase PersonaDl y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 9/12/2025
 */
interface PersonaSacdRepositoryInterface
{

    /**
     * @return array{0: string, 1: string, 2: string, 3: array<int|string, string>}
     */
    public function getArraySacdyCheckBox(int $Qseleccion_sacd): array;

    /**
     * @return list<PersonaSacd>
     */
    public function getSacdsBySelect(int $Qseleccion_sacd): array;

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


    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<PersonaSacd>
     */
    public function getPersonas(array $aWhere = [], array $aOperators = []): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    public function Eliminar(PersonaSacd $PersonaSacd): bool;

    public function Guardar(PersonaSacd $PersonaSacd): bool;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_nom): array|false;

    public function findById(int $id_nom): ?PersonaSacd;

}
