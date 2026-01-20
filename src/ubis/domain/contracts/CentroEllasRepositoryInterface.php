<?php

namespace src\ubis\domain\contracts;

use PDO;
use src\ubis\domain\entity\CentroEllas;

/**
 * Interfaz de la clase CentroEllas y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 21/11/2025
 */
interface CentroEllasRepositoryInterface
{

    public function getArrayCentros($sCondicion = ''): array;
/* --------------------  BASiC SEARCH ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo CentroEllas
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|false Una colección de objetos de tipo CentroEllas
	
	 */
	public function getCentros(array $aWhere=[], array $aOperators=[]): array|false;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(CentroEllas $CentroEllas): bool;

	public function Guardar(CentroEllas $CentroEllas): bool;

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
    public function findById(int $id_ubi): ?CentroEllas;
}