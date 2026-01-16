<?php

namespace shared\domain\repositories;

use PDO;
use src\shared\domain\entity\ColaMail;


/**
 * Interfaz de la clase ColaMail y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 1/3/2024
 */
interface ColaMailRepositoryInterface
{

/* --------------------  BASiC SEARCH ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo ColaMail
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo ColaMail
	
	 */
	public function getColaMails(array $aWhere=[], array $aOperators=[]): array|FALSE;

	public function deleteColaMails(string $date_iso): void;

/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(ColaMail $ColaMail): bool;

	public function Guardar(ColaMail $ColaMail): bool;

	public function getErrorTxt(): string;

	public function getoDbl(): PDO;

	public function setoDbl(PDO $oDbl): void;

	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param  $uuid_item
     * @return array|bool
	
     */
    public function datosById( $uuid_item): array|bool;
	
    /**
     * Busca la clase con uuid_item en el repositorio.
	
     */
    public function findById( $uuid_item): ?ColaMail;
}