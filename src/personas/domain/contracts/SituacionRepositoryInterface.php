<?php

namespace src\personas\domain\contracts;

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

    /**
     * @return array<string, string>
     */
    public function getArraySituaciones(bool $traslado = false): array;

/* --------------------  BASiC SEARCH ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo Situacion
	 *
	 * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return list<Situacion> Una colección de objetos de tipo Situacion
	
	 */
	public function getSituaciones(array $aWhere=[], array $aOperators=[]): array;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Situacion $Situacion): bool;

	public function Guardar(Situacion $Situacion): bool;

	public function getErrorTxt(): string;

	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param string $situacion
     * @return array<string, mixed>|false
	
     */
    public function datosById(string $situacion): array|false;
	
    /**
     * Busca la clase con situacion en el repositorio.
	
     */
    public function findById(string $situacion): ?Situacion;
}
