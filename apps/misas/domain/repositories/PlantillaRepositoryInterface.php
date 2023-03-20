<?php

namespace misas\domain\repositories;

use PDO;
use misas\domain\entity\Plantilla;
use web\Desplegable;


use web\TimeLocal;
use web\NullTimeLocal;
use core\ConverterDate;
/**
 * Interfaz de la clase Plantilla y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/3/2023
 */
interface PlantillaRepositoryInterface
{

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo Plantilla
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo Plantilla
	
	 */
	public function getPlantillas(array $aWhere=[], array $aOperators=[]): array|FALSE;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Plantilla $Plantilla): bool;

	public function Guardar(Plantilla $Plantilla): bool;

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
    public function findById(int $id_item): ?Plantilla;
	
    public function getNewId_item();
}