<?php

namespace src\inventario\domain\contracts;

use PDO;
use src\inventario\domain\entity\UbiInventario;

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

    public function getUbisInventarioLugar($bLugar): false|array;
    public function getArrayUbisInventario():array|false;

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo UbiInventario
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|false Una colección de objetos de tipo UbiInventario
	
	 */
	public function getUbisInventario(array $aWhere=[], array $aOperators=[]): array|false;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(UbiInventario $UbiInventario): bool;

	public function Guardar(UbiInventario $UbiInventario): bool;

	public function getErrorTxt(): string;

	public function getoDbl(): PDO;

	public function setoDbl(PDO $oDbl): void;

	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param int $id_ubi
     * @return array|bool
	
     */
    public function datosById(int $id_ubi): array|bool;
	
    /**
     * Busca la clase con id_ubi en el repositorio.
	
     */
    public function findById(int $id_ubi): ?UbiInventario;
	
    public function getNewId();
}