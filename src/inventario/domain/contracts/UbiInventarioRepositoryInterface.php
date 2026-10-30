<?php

namespace src\inventario\domain\contracts;

use src\inventario\domain\entity\UbiInventario;
use src\shared\domain\criteria\Criteria;

/**
 * Interfaz de la clase UbiInventario y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
interface UbiInventarioRepositoryInterface
{

    /**
     * @return list<UbiInventario>
     */
    public function getUbisInventarioLugar(bool $bLugar): array;
    /**
     * @return array<int|string, string>
     */
    public function getArrayUbisInventario(): array;

/* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * UbiInventario que cumplen el criterio.
     *
     * @return list<UbiInventario>
     */
    public function matching(Criteria $criteria): array;

/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(UbiInventario $UbiInventario): bool;

	public function Guardar(UbiInventario $UbiInventario): bool;

	public function getErrorTxt(): string;



	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param int $id_ubi
     * @return array<string, mixed>|false
	
     */
    public function datosById(int $id_ubi): array|false;
	
    /**
     * Busca la clase con id_ubi en el repositorio.
	
     */
    public function findById(int $id_ubi): ?UbiInventario;
	
    public function getNewId(): int;
}