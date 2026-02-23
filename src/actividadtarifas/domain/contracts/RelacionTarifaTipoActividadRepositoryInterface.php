<?php

namespace src\actividadtarifas\domain\contracts;

use PDO;
use src\actividadtarifas\domain\entity\RelacionTarifaTipoActividad;


/**
 * Interfaz de la clase RelacionTarifaTipoActividad y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 3/12/2025
 */
interface RelacionTarifaTipoActividadRepositoryInterface
{

/* --------------------  BASiC SEARCH ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo RelacionTarifaTipoActividad
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|false Una colección de objetos de tipo RelacionTarifaTipoActividad
	
	 */
	public function getTipoActivTarifas(array $aWhere=[], array $aOperators=[]): array|false;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(RelacionTarifaTipoActividad $RelacionTarifaTipoActividad): bool;

	public function Guardar(RelacionTarifaTipoActividad $RelacionTarifaTipoActividad): bool;

	public function getErrorTxt(): string;

	public function getoDbl(): PDO;

	public function setoDbl(PDO $oDbl): void;

	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param int $id_item
     * @return array|bool
	
     */
    public function datosById(int $id_item): array|bool;
	
    /**
     * Busca la clase con id_item en el repositorio.
	
     */
    public function findById(int $id_item): ?RelacionTarifaTipoActividad;
	
    public function getNewId();
}