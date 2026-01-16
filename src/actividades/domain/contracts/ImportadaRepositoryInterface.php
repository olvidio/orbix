<?php

namespace src\actividades\domain\contracts;

use PDO;
use src\actividades\domain\entity\Importada;


/**
 * Interfaz de la clase Importada y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 9/12/2025
 */
interface ImportadaRepositoryInterface
{

/* --------------------  BASiC SEARCH ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo Importada
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|false Una colección de objetos de tipo Importada
	
	 */
	public function getImportadas(array $aWhere=[], array $aOperators=[]): array|false;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Importada $Importada): bool;

	public function Guardar(Importada $Importada): bool;

	public function getErrorTxt(): string;

	public function getoDbl(): PDO;

	public function setoDbl(PDO $oDbl): void;

	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param int $id_activ
     * @return array|bool
	
     */
    public function datosById(int $id_activ): array|bool;
	
    /**
     * Busca la clase con id_activ en el repositorio.
	
     */
    public function findById(int $id_activ): ?Importada;
}