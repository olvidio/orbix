<?php

namespace src\profesores\domain\contracts;

use src\shared\domain\criteria\Criteria;
use src\profesores\domain\entity\ProfesorLatin;


/**
 * Interfaz de la clase ProfesorLatin y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 29/11/2025
 */
interface ProfesorLatinRepositoryInterface
{

/* --------------------  BASiC SEARCH ---------------------------------------- */

	/**
	 * @return list<ProfesorLatin>
	 */
	public function matching(Criteria $criteria): array;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(ProfesorLatin $ProfesorLatin): bool;

	public function Guardar(ProfesorLatin $ProfesorLatin): bool;

	public function getErrorTxt(): string;



	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param int $id_nom
     * @return array<string, mixed>|false
	
     */
    public function datosById(int $id_nom): array|false;
	
    /**
     * Busca la clase con id_nom en el repositorio.
	
     */
    public function findById(int $id_nom): ?ProfesorLatin;
}