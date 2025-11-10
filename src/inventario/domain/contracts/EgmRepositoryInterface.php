<?php

namespace src\inventario\domain\contracts;

use PDO;
use src\inventario\domain\entity\Egm;
use src\inventario\domain\value_objects\EgmItemId;


/**
 * Interfaz de la clase Egm y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
interface EgmRepositoryInterface
{

    public function getArrayIdFromIdEquipajes($aEquipajes, $lugar = ''): array;
    public function getUltimoGrupo(int $id_equipaje): int;

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo Egm
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo Egm
	
	 */
	public function getEgmes(array $aWhere=[], array $aOperators=[]): array|FALSE;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Egm $Egm): bool;

	public function Guardar(Egm $Egm): bool;

	public function getErrorTxt(): string;

	public function getoDbl(): PDO;

	public function setoDbl(PDO $oDbl): void;

	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param EgmItemId $id_item
     * @return array|bool
	
     */
    public function datosById(EgmItemId $id_item): array|bool;
	
    /**
     * Busca la clase con id_item en el repositorio.
	
     */
    public function findById(EgmItemId $id_item): ?Egm;
	
    public function getNewId();
}