<?php

namespace src\actividadcargos\domain\contracts;

use PDO;
use src\actividadcargos\domain\entity\Cargo;

use function core\is_true;
/**
 * Interfaz de la clase Cargo y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 17/11/2025
 */
interface CargoRepositoryInterface
{
    public function getArrayIdCargosSacd(): array;

    public function getArrayCargos(string $tipo_cargo = ''): array;

/* --------------------  BASiC SEARCH ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo Cargo
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|false Una colección de objetos de tipo Cargo
	
	 */
	public function getCargos(array $aWhere=[], array $aOperators=[]): array|false;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Cargo $Cargo): bool;

	public function Guardar(Cargo $Cargo): bool;

	public function getErrorTxt(): string;

	public function getoDbl(): PDO;

	public function setoDbl(PDO $oDbl): void;

	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param int $id_cargo
     * @return array|bool
	
     */
    public function datosById(int $id_cargo): array|bool;
	
    /**
     * Busca la clase con id_cargo en el repositorio.
	
     */
    public function findById(int $id_cargo): ?Cargo;
	
    public function getNewId();
}