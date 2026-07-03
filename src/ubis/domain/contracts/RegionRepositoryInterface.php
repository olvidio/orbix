<?php

namespace src\ubis\domain\contracts;

use src\ubis\domain\entity\Region;

/**
 * Interfaz de la clase Region y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 5/11/2025
 */
interface RegionRepositoryInterface
{

/* --------------------  BASiC SEARCH ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo Region
	 *
	 * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return list<Region> Una colección de objetos de tipo Region
	
	 */
	public function getRegiones(array $aWhere=[], array $aOperators=[]): array;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Region $Region): bool;

	public function Guardar(Region $Region): bool;

	public function getErrorTxt(): string;



	public function getNomTabla(): string;
	
    /**
     * @return array<string, mixed>|false
     */
    /**
     * @return array<string, mixed>|false
     */
    /**
     * @return array<string, mixed>|false
     */
    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_region): array|false;
	
    public function findById(int $id_region): ?Region;
	
    public function getNewId(): int;
}