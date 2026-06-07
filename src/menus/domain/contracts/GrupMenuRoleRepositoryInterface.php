<?php

namespace src\menus\domain\contracts;

use src\menus\domain\entity\GrupMenuRole;

/**
 * Interfaz de la clase GrupMenuRole y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/4/2025
 */
interface GrupMenuRoleRepositoryInterface
{

/* --------------------  BASiC SEARCH ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo GrupMenuRole
	 *
	 * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return list<GrupMenuRole> Una colección de objetos de tipo GrupMenuRole
	
	 */
	public function getGrupMenuRoles(array $aWhere=[], array $aOperators=[]): array;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(GrupMenuRole $GrupMenuRole): bool;

	public function Guardar(GrupMenuRole $GrupMenuRole): bool;

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
    public function findById(int $id_item): ?GrupMenuRole;
	
    public function getNewId(): int;
}