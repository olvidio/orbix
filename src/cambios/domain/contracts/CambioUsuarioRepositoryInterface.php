<?php

namespace src\cambios\domain\contracts;

use src\cambios\domain\entity\CambioUsuario;
use src\shared\domain\value_objects\DateTimeLocal;


/**
 * Interfaz de la clase CambioUsuario y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/12/2025
 */
interface CambioUsuarioRepositoryInterface
{

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<CambioUsuario>
     */
    public function getCambiosUsuario(array $aWhere = [], array $aOperators = []): array;

    public function eliminarHastaFecha(DateTimeLocal|string $df_fin): bool;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(CambioUsuario $CambioUsuario): bool;

    public function Guardar(CambioUsuario $CambioUsuario): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_item): array|false;

    public function findById(int $id_item): ?CambioUsuario;

    public function getNewId(): int;
}
