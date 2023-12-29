<?php

namespace misas\domain\repositories;

use misas\domain\entity\InicialesSacd;
use PDO;

/**
 * Interfaz de la clase InicialesSacd y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/3/2023
 */
interface InicialesSacdRepositoryInterface
{

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo InicialesSacd
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo InicialesSacd
	
	 */
	public function getInicialesSacd(array $aWhere=[], array $aOperators=[]): array|FALSE;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(InicialesSacd $InicialesSacd): bool;

	public function Guardar(InicialesSacd $InicialesSacd): bool;

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
     * Busca la clase con id_item en el repositorio.
	
     */
    public function findById(int $id_nom): ?InicialesSacd;
	
}