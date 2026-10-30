<?php

namespace src\encargossacd\domain\contracts;

use src\encargossacd\domain\entity\EncargoSacdObserv;
use src\shared\domain\criteria\Criteria;


/**
 * Interfaz de la clase EncargoSacdObserv y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 23/12/2025
 */
interface EncargoSacdObservRepositoryInterface
{

/* --------------------  BASiC SEARCH ---------------------------------------- */

	/**
	 * EncargoSacdObserv que cumplen el criterio.
	 *
	 * @return list<EncargoSacdObserv>
	 */
	public function matching(Criteria $criteria): array;

	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(EncargoSacdObserv $EncargoSacdObserv): bool;

	public function Guardar(EncargoSacdObserv $EncargoSacdObserv): bool;

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
    public function findById(int $id_item): ?EncargoSacdObserv;
	
    public function getNewId(): int;
}