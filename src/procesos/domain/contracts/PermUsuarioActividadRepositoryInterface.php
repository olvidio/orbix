<?php

namespace src\procesos\domain\contracts;

use src\shared\domain\criteria\Criteria;
use src\procesos\domain\entity\PermUsuarioActividad;


/**
 * Interfaz de la clase PermUsuarioActividad y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/12/2025
 */
interface PermUsuarioActividadRepositoryInterface
{

/* --------------------  BASiC SEARCH ---------------------------------------- */

	/**
	 * @return list<PermUsuarioActividad>
	 */
	public function matching(Criteria $criteria): array;

/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(PermUsuarioActividad $PermUsuarioActividad): bool;

	public function Guardar(PermUsuarioActividad $PermUsuarioActividad): bool;

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
    public function findById(int $id_item): ?PermUsuarioActividad;
	
    public function getNewId(): int;
}