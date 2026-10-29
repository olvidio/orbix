<?php

namespace src\inventario\domain\contracts;

use src\inventario\domain\entity\Egm;
use src\shared\domain\criteria\Criteria;

/**
 * Interfaz de la clase Egm y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
interface EgmRepositoryInterface
{

    /**
     * @param array<int, int|string> $aEquipajes
     * @return list<string>
     */
    public function getArrayIdFromIdEquipajes(array $aEquipajes, int|string $lugar = ''): array;

    public function getUltimoGrupo(int $id_equipaje): int;

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * Egm que cumplen el criterio.
     *
     * @return list<Egm>
     */
    public function matching(Criteria $criteria): array;

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<Egm>
     */
    public function getEgmes(array $aWhere = [], array $aOperators = []): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Egm $Egm): bool;

    public function Guardar(Egm $Egm): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_item
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_item): array|false;

    /**
     * Busca la clase con id_item en el repositorio.
     */
    public function findById(int $id_item): ?Egm;

    public function getNewId(): int;
}