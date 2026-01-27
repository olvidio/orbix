<?php

namespace src\personas\domain\contracts;

use PDO;
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

    public function getArraySacdyCheckBox(int $Qseleccion_sacd): array;

    public function getSacdsBySelect(int $Qseleccion_sacd): array;

    /**
     * Devuelve un array con los id de centros (id_ctr) de personas activas.
     *
     * @param string $sdonde condición extra SQL (debe empezar por AND)
     */
    public function getArrayIdCentros(string $sdonde = ''): array;

    /**
     * Lista de posibles SACD en array [id_nom => ape_nom].
     *
     * @param string $sdonde condición extra SQL (debe empezar por AND)
     */
    public function getArraySacd(string $sdonde = ''): array;

    /**
     * Lista de personas activas en array [id_nom => ape_nom(centro)].
     *
     */
    public function getArrayPersonas(string $id_tabla = ''): array;


    public function getPersonas(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function getErrorTxt(): string;

    public function getoDbl(): PDO;

    public function setoDbl(PDO $oDbl): void;

    public function getNomTabla(): string;

    public function Eliminar(PersonaSacd $PersonaSacd): bool;

    public function Guardar(PersonaSacd $PersonaSacd): bool;

    public function datosById(int $id_nom): array|bool;

    public function findById(int $id_nom): ?PersonaSacd;

}