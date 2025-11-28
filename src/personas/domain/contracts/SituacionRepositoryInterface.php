<?php

namespace src\personas\domain\contracts;

use PDO;
use src\personas\domain\entity\Situacion;

/**
 * Interfaz de la clase Situacion y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 17/11/2025
 */
interface SituacionRepositoryInterface
{

    public function getArraySituaciones($traslado = false);

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo Situacion
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|false Una colección de objetos de tipo Situacion
	
	 */
	public function getSituaciones(array $aWhere=[], array $aOperators=[]): array|false;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Situacion $Situacion): bool;

	public function Guardar(Situacion $Situacion): bool;

	public function getErrorTxt(): string;

	public function getoDbl(): PDO;

	public function setoDbl(PDO $oDbl): void;

	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param string $situacion
     * @return array|bool
	
     */
    public function datosById(string $situacion): array|bool;
	
    /**
     * Busca la clase con situacion en el repositorio.
	
     */
    public function findById(string $situacion): ?Situacion;
}