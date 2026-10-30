<?php

namespace src\profesores\domain\contracts;

use src\shared\domain\criteria\Criteria;
use src\profesores\domain\entity\ProfesorTituloEst;


/**
 * Interfaz de la clase ProfesorTituloEst y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 29/11/2025
 */
interface ProfesorTituloEstRepositoryInterface
{

/* --------------------  BASiC SEARCH ---------------------------------------- */

	/**
	 * @return list<ProfesorTituloEst>
	 */
	public function matching(Criteria $criteria): array;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(ProfesorTituloEst $ProfesorTituloEst): bool;

	public function Guardar(ProfesorTituloEst $ProfesorTituloEst): bool;

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
    public function findById(int $id_item): ?ProfesorTituloEst;
	
    public function getNewId(): int;
}