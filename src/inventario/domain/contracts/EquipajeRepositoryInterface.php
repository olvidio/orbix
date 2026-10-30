<?php

namespace src\inventario\domain\contracts;

use src\inventario\domain\entity\Equipaje;
use src\shared\domain\criteria\Criteria;

/**
 * Interfaz de la clase Equipaje y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
interface EquipajeRepositoryInterface
{

    /**
     * @return list<int>
     */
    public function getEquipajesCoincidentes(string $f_ini_iso, string $f_fin_iso): array;

    /**
     * @return array<int|string, string>
     */
    public function getArrayEquipajes(string $f_ini_iso = ''): array;

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * Equipaje que cumplen el criterio.
     *
     * @return list<Equipaje>
     */
    public function matching(Criteria $criteria): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Equipaje $Equipaje): bool;

    public function Guardar(Equipaje $Equipaje): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_equipaje
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_equipaje): array|false;

    /**
     * Busca la clase con id_equipaje en el repositorio.
     */
    public function findById(int $id_equipaje): ?Equipaje;

    public function getNewId(): int;
}