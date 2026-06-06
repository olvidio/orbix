<?php

namespace src\cambios\domain\contracts;

use src\cambios\domain\entity\CambioUsuarioObjetoPref;


/**
 * Interfaz de la clase CambioUsuarioObjetoPref y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/12/2025
 */
interface CambioUsuarioObjetoPrefRepositoryInterface
{

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<CambioUsuarioObjetoPref>
     */
    public function getCambioUsuarioObjetoPrefs(array $aWhere = [], array $aOperators = []): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(CambioUsuarioObjetoPref $CambioUsuarioObjetoPref): bool;

    public function Guardar(CambioUsuarioObjetoPref $CambioUsuarioObjetoPref): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_item_usuario_objeto): array|false;

    public function findById(int $id_item_usuario_objeto): ?CambioUsuarioObjetoPref;

    public function getNewId(): int;
}
