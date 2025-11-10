<?php

namespace src\inventario\domain\contracts;

use PDO;
use src\inventario\domain\entity\Whereis;
use src\inventario\domain\value_objects\WhereisItemId;


/**
 * Interfaz de la clase Whereis y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
interface WhereisRepositoryInterface
{

    public function getArrayIdFromIdEgms(array $aEgms):array;

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo Whereis
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo Whereis
	
	 */
	public function getWhereare(array $aWhere=[], array $aOperators=[]): array|FALSE;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Whereis $Whereis): bool;

	public function Guardar(Whereis $Whereis): bool;

	public function getErrorTxt(): string;

	public function getoDbl(): PDO;

	public function setoDbl(PDO $oDbl): void;

	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param WhereisItemId $id_item_whereis
     * @return array|bool
	
     */
    public function datosById(WhereisItemId $id_item_whereis): array|bool;
	
    /**
     * Busca la clase con id_item_whereis en el repositorio.
	
     */
    public function findById(WhereisItemId $id_item_whereis): ?Whereis;
	
    public function getNewId();
}