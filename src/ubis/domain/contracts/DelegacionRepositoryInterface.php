<?php

namespace src\ubis\domain\contracts;

use PDO;
use src\ubis\domain\entity\Delegacion;


use function core\is_true;
/**
 * Interfaz de la clase Delegacion y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 5/11/2025
 */
interface DelegacionRepositoryInterface
{

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo Delegacion
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo Delegacion
	
	 */
	public function getDelegaciones(array $aWhere=[], array $aOperators=[]): array|FALSE;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Delegacion $Delegacion): bool;

	public function Guardar(Delegacion $Delegacion): bool;

	public function getErrorTxt(): string;

	public function getoDbl(): PDO;

	public function setoDbl(PDO $oDbl): void;

	public function getNomTabla(): string;
	
    public function datosById(int $id_dl): array|bool;
	
    /**
     * Busca la clase con dl en el repositorio.
	
     */
    public function findById(int $id_dl): ?Delegacion;
}