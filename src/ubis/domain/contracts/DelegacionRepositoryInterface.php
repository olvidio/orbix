<?php

namespace src\ubis\domain\contracts;

use src\ubis\domain\entity\Delegacion;

/**
 * Interfaz de la clase Delegacion y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 5/11/2025
 */
interface DelegacionRepositoryInterface
{

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Delegacion
     *
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<Delegacion> Una colección de objetos de tipo Delegacion
     */
    public function getDelegaciones(array $aWhere = [], array $aOperators = []): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Delegacion $Delegacion): bool;

    public function Guardar(Delegacion $Delegacion): bool;

    public function getErrorTxt(): string;


    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    /**
     * @return array<string, mixed>|false
     */
    /**
     * @return array<string, mixed>|false
     */
    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_dl): array|false;

    /**
     * Busca la clase con dl en el repositorio.
     */
    public function findById(int $id_dl): ?Delegacion;

    public function getNewId(): int;

    /* -------------------- MÉTODOS ADICIONALES (legacy utilidades) ---------- */

    /**
     * Indica si la delegación (sigla) es la propia región STGR especial.
     * Devuelve true para casos especiales H y M.
     */
    public function soy_region_stgr(string $dele = ''): bool;

    /**
     * Devuelve información de región STGR y esquemas relacionados de la dl dada.
     * Retorna array asociativo con claves: region_stgr, esquema_region_stgr, mi_id_schema, esquema_dl
     */
    /**
     * @return array<string, mixed>
     */
    /**
     * @return array<string, mixed>
     */
    public function mi_region_stgr(string $dele = ''): array;

    /**
     * Devuelve un array [schema => id] para la región STGR indicada (incluye la propia).
     */
    /**
     * @return array<int|string, string>
     */
    public function getArrayIdSchemaRegionStgr(string $sRegionStgr, int $mi_sfsv): array;

    /**
     * Devuelve un array de nombres de esquemas pertenecientes al grupo STGR de la región indicada.
     * Si $mi_sfsv es null, devuelve los esquemas "comunes".
     */
    /**
     * @return array<int|string, string>
     */
    public function getArraySchemasRegionStgr(string $sRegionStgr, ?int $mi_sfsv = null): array;

    /**
     * Devuelve array [id_dl => dl] filtrado por regiones STGR indicadas.
     */
    /**
     * @param list<string> $aRegiones
     * @return array<int|string, string>
     */
    public function getArrayDlRegionStgr(array $aRegiones = []): array;

}