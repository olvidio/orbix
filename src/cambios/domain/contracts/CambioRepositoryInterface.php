<?php

namespace src\cambios\domain\contracts;

use JsonException;
use src\cambios\domain\entity\Cambio;


/**
 * Interfaz de la clase CambioDl y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 19/12/2025
 */
interface CambioRepositoryInterface
{

    public function getNomActivEliminada(int $iId): string;

    public function borrarCambios(string $str_interval = 'P1Y'): void;

    /**
     * @return list<Cambio>
     */
    public function getCambiosNuevos(): array;

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<Cambio>
     * @throws JsonException
     */
    public function getCambios(array $aWhere = [], array $aOperators = []): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Cambio $Cambio): bool;

    public function Guardar(Cambio $Cambio): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     * @throws JsonException
     */
    public function datosById(int $id_item_cambio): array|false;

    /**
     * @throws JsonException
     */
    public function findById(int $id_item_cambio): ?Cambio;

    public function getNewId(): int;
}
