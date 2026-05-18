<?php

namespace src\personas\domain\contracts;

use src\personas\domain\entity\PersonaPub;


/**
 * Interfaz de la clase PersonaDl y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 9/12/2025
 */
interface PersonaPubRepositoryInterface
{

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


    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo PersonaDl
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|bool Una colección de objetos de tipo PersonaDl
     */
    public function getPersonas(array $aWhere = [], array $aOperators = []): array|bool;

    /**
     * Como getPersonas: incluye todas las filas aunque falte región stgr de la dl;
     * acumula problemas en $problemasRegionStgr (usar RegionStgrAviso::formatear al mostrar).
     *
     * @param-out array<string, array<string, string>> $problemasRegionStgr
     * @param-out array<int, true> $sinRegionStgrPorIdNom id_nom marcados sin región stgr en la dl
     * @return array<int, PersonaPub>
     */
    public function getPersonasParaListado(
        array $aWhere,
        array $aOperators,
        array &$problemasRegionStgr,
        array &$sinRegionStgrPorIdNom = [],
    ): array;

    /**
     * Como findById pero tolera dl sin región stgr (id_schema=0 y aviso).
     *
     * @param-out array<string, array<string, string>> $problemasRegionStgr
     * @param-out bool $marcaAvisoRegionStgr true si la fila se cargó sin región stgr
     */
    public function findByIdParaListado(int $id_nom, array &$problemasRegionStgr, bool &$marcaAvisoRegionStgr): ?PersonaPub;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_nom
     * @return array|bool
     */
    public function datosById(int $id_nom): array|bool;

    /**
     * Busca la clase con id_nom en el repositorio.
     */
    public function findById(int $id_nom): ?PersonaPub;
}
