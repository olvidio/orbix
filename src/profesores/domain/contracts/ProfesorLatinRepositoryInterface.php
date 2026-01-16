<?php

namespace src\profesores\domain\contracts;

use PDO;
use src\profesores\domain\entity\ProfesorLatin;


use function core\is_true;
/**
 * Interfaz de la clase ProfesorLatin y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 29/11/2025
 */
interface ProfesorLatinRepositoryInterface
{

/* --------------------  BASiC SEARCH ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo ProfesorLatin
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|false Una colección de objetos de tipo ProfesorLatin
	
	 */
	public function getProfesoresLatin(array $aWhere=[], array $aOperators=[]): array|false;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(ProfesorLatin $ProfesorLatin): bool;

	public function Guardar(ProfesorLatin $ProfesorLatin): bool;

	public function getErrorTxt(): string;

	public function getoDbl(): PDO;

	public function setoDbl(PDO $oDbl): void;

	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param int $id_nom
     * @return array|bool
	
     */
    public function datosById(int $id_nom): array|bool;
	
    /**
     * Busca la clase con id_nom en el repositorio.
	
     */
    public function findById(int $id_nom): ?ProfesorLatin;
}