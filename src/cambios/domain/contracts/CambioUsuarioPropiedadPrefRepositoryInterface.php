<?php

namespace src\cambios\domain\contracts;

use src\cambios\domain\entity\CambioUsuarioPropiedadPref;


/**
 * Interfaz de la clase CambioUsuarioPropiedadPref y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/12/2025
 */
interface CambioUsuarioPropiedadPrefRepositoryInterface
{

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<CambioUsuarioPropiedadPref>
     */
    public function getCambioUsuarioPropiedadPrefs(array $aWhere = [], array $aOperators = []): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(CambioUsuarioPropiedadPref $CambioUsuarioPropiedadPref): bool;

    public function Guardar(CambioUsuarioPropiedadPref $CambioUsuarioPropiedadPref): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_item): array|false;

    public function findById(int $id_item): ?CambioUsuarioPropiedadPref;

    public function getNewId(): int;
}
