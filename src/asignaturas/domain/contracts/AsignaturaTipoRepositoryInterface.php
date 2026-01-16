<?php

namespace src\asignaturas\domain\contracts;

use PDO;
use src\asignaturas\domain\entity\AsignaturaTipo;

/**
 * Interfaz de la clase AsignaturaTipo y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 14/11/2025
 */
interface AsignaturaTipoRepositoryInterface
{

    function getArrayAsignaturaTipos(): array;
/* --------------------  BASiC SEARCH ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo AsignaturaTipo
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|false Una colección de objetos de tipo AsignaturaTipo
	
	 */
	public function getAsignaturaTipos(array $aWhere=[], array $aOperators=[]): array|false;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(AsignaturaTipo $AsignaturaTipo): bool;

	public function Guardar(AsignaturaTipo $AsignaturaTipo): bool;

	public function getErrorTxt(): string;

	public function getoDbl(): PDO;

	public function setoDbl(PDO $oDbl): void;

	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param int $id_tipo
     * @return array|bool
	
     */
    public function datosById(int $id_tipo): array|bool;
	
    /**
     * Busca la clase con id_tipo en el repositorio.
	
     */
    public function findById(int $id_tipo): ?AsignaturaTipo;
}