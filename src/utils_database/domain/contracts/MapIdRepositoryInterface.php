<?php

namespace src\utils_database\domain\contracts;

use src\shared\domain\criteria\Criteria;
use src\utils_database\domain\entity\MapId;

/**
 * Interfaz de la clase MapId y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 13/11/2025
 */
interface MapIdRepositoryInterface
{

/* --------------------  BASiC SEARCH ---------------------------------------- */

	/**
	 * MapId que cumplen el criterio.
	 *
	 * @return list<MapId>
	 */
	public function matching(Criteria $criteria): array;

/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(MapId $MapId): bool;

	public function Guardar(MapId $MapId): bool;

	public function getErrorTxt(): string;



	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param string $objeto
     * @return array<string, mixed>|false
     */
    public function datosById(string $objeto, int $id_resto): array|false;
	
    /**
     * Busca la clase con objeto en el repositorio.
	
     */
    public function findById(string $objeto, int $id_resto): ?MapId;
}