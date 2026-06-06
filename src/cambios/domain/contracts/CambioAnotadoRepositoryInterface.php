<?php

namespace src\cambios\domain\contracts;

use src\cambios\domain\entity\CambioAnotado;


/**
 * Interfaz de la clase CambioAnotado y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/12/2025
 */
interface CambioAnotadoRepositoryInterface
{

    public function setTabla(string $ubicacion): void;

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<CambioAnotado>
     */
    public function getCambiosAnotados(array $aWhere = [], array $aOperators = []): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(CambioAnotado $CambioAnotado): bool;

    public function Guardar(CambioAnotado $CambioAnotado): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_item): array|false;

    public function findById(int $id_item): ?CambioAnotado;

    public function getNewId(): int;
}
