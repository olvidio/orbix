<?php

namespace src\inventario\domain\contracts;

use PDO;
use src\inventario\domain\entity\Lugar;
use src\inventario\domain\value_objects\LugarId;

/**
 * Interfaz de la clase Lugar y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
interface LugarRepositoryInterface
{

    public function getArrayLugares(int $id_ubi):array;

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo Lugar
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|false Una colección de objetos de tipo Lugar
	
	 */
	public function getLugares(array $aWhere=[], array $aOperators=[]): array|false;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Lugar $Lugar): bool;

	public function Guardar(Lugar $Lugar): bool;

	public function getErrorTxt(): string;

	public function getoDbl(): PDO;

	public function setoDbl(PDO $oDbl): void;

	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param LugarId $id_lugar
     * @return array|bool
	
     */
    public function datosById(LugarId $id_lugar): array|bool;
	
    /**
     * Busca la clase con id_lugar en el repositorio.
	
     */
    public function findById(LugarId $id_lugar): ?Lugar;
	
    public function getNewId();
}