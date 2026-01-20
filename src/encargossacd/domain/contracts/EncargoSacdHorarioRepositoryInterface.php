<?php

namespace src\encargossacd\domain\contracts;

use PDO;
use src\encargossacd\domain\entity\EncargoSacdHorario;


/**
 * Interfaz de la clase EncargoSacdHorario y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 23/12/2025
 */
interface EncargoSacdHorarioRepositoryInterface
{

/* --------------------  BASiC SEARCH ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo EncargoSacdHorario
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|false Una colección de objetos de tipo EncargoSacdHorario
	
	 */
	public function getEncargoSacdHorarios(array $aWhere=[], array $aOperators=[]): array|false;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(EncargoSacdHorario $EncargoSacdHorario): bool;

	public function Guardar(EncargoSacdHorario $EncargoSacdHorario): bool;

	public function getErrorTxt(): string;

	public function getoDbl(): PDO;

	public function setoDbl(PDO $oDbl): void;

	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param int $id_item
     * @return array|bool
	
     */
    public function datosById(int $id_item): array|bool;
	
    /**
     * Busca la clase con id_item en el repositorio.
	
     */
    public function findById(int $id_item): ?EncargoSacdHorario;
	
    public function getNewId();
}