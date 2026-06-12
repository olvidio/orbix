<?php

namespace src\configuracion\domain\contracts;

use src\configuracion\domain\entity\ModuloInstalado;
use src\shared\domain\contracts\DatosCrudRepositoryInterface;

/**
 * Interfaz de la clase ModuloInstalado y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 13/11/2025
 */
interface ModuloInstaladoRepositoryInterface extends DatosCrudRepositoryInterface
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
     * devuelve una colección (array) de objetos de tipo ModuloInstalado
     *
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<ModuloInstalado> Una colección de objetos de tipo ModuloInstalado
     */
    public function getModuloInstalados(array $aWhere = [], array $aOperators = []): array;

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
    public function findById(mixed $id): ?ModuloInstalado;
}