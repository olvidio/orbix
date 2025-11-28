<?php

namespace src\asignaturas\domain\contracts;

use PDO;
use src\asignaturas\domain\entity\Sector;

/**
 * Interfaz de la clase Sector y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 14/11/2025
 */
interface SectorRepositoryInterface
{

    public function getArraySectoresPorDepartamento(): array;
    public function getArraySectores(): array;

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo Sector
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|false Una colección de objetos de tipo Sector
	
	 */
	public function getSectores(array $aWhere=[], array $aOperators=[]): array|false;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Sector $Sector): bool;

	public function Guardar(Sector $Sector): bool;

	public function getErrorTxt(): string;

	public function getoDbl(): PDO;

	public function setoDbl(PDO $oDbl): void;

	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param int $id_sector
     * @return array|bool
	
     */
    public function datosById(int $id_sector): array|bool;
	
    /**
     * Busca la clase con id_sector en el repositorio.
	
     */
    public function findById(int $id_sector): ?Sector;
	
    public function getNewId();
}