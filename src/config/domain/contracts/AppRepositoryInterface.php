<?php

namespace src\config\domain\contracts;

use PDO;
use src\config\domain\entity\App;


/**
 * Interfaz de la clase App y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 10/11/2025
 */
interface AppRepositoryInterface
{

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo App
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo App
	
	 */
	public function getApps(array $aWhere=[], array $aOperators=[]): array|FALSE;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(App $App): bool;

	public function Guardar(App $App): bool;

	public function getErrorTxt(): string;

	public function getoDbl(): PDO;

	public function setoDbl(PDO $oDbl): void;

	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param int $id_app
     * @return array|bool
	
     */
    public function datosById(int $id_app): array|bool;
	
    /**
     * Busca la clase con id_app en el repositorio.
	
     */
    public function findById(int $id_app): ?App;
	
    public function getNewId();
}