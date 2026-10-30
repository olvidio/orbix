<?php

namespace src\configuracion\domain\contracts;

use src\configuracion\domain\entity\ModuloInstalado;
use src\shared\domain\criteria\Criteria;

/**
 * Interfaz de la clase ModuloInstalado y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 13/11/2025
 */
interface ModuloInstaladoRepositoryInterface
{

    /**
     * @return array<int|string, string>
     */
    /**
     * @return array<int|string, string>
     */
    public function getArrayModulosInstalados(): array;

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * Módulos instalados que cumplen el criterio.
     *
     * @return list<ModuloInstalado>
     */
    public function matching(Criteria $criteria): array;


    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ModuloInstalado $ModuloInstalado): bool;

    public function Guardar(ModuloInstalado $ModuloInstalado): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_mod
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_mod): array|false;

    /**
     * Busca la clase con id_mod en el repositorio.
     */
    public function findById(int $id_mod): ?ModuloInstalado;
}