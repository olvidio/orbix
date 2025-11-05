<?php

namespace src\ubis\domain\contracts;

use PDO;
use src\ubis\domain\entity\Region;


use function core\is_true;
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

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo Region
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo Region
	
	 */
	public function getRegiones(array $aWhere=[], array $aOperators=[]): array|FALSE;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Region $Region): bool;

	public function Guardar(Region $Region): bool;

	public function getErrorTxt(): string;

	public function getoDbl(): PDO;

	public function setoDbl(PDO $oDbl): void;

	public function getNomTabla(): string;
	
    public function datosById(int $id_region): array|bool;
	
    public function findById(int $id_region): ?Region;
	
    public function getNewId();
}