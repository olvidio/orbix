<?php

namespace src\inventario\domain\contracts;

use PDO;
use src\inventario\domain\entity\Coleccion;
use src\inventario\domain\value_objects\ColeccionId;

/**
 * Interfaz de la clase Coleccion y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
interface ColeccionRepositoryInterface
{

    public function getArrayColecciones(): array;

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo Coleccion
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|false Una colección de objetos de tipo Coleccion
	
	 */
	public function getColecciones(array $aWhere=[], array $aOperators=[]): array|false;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Coleccion $Coleccion): bool;

	public function Guardar(Coleccion $Coleccion): bool;

	public function getErrorTxt(): string;

	public function getoDbl(): PDO;

	public function setoDbl(PDO $oDbl): void;

	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param ColeccionId $id_coleccion
     * @return array|bool
	
     */
    public function datosById(ColeccionId $id_coleccion): array|bool;
	
    /**
     * Busca la clase con id_coleccion en el repositorio.
	
     */
    public function findById(ColeccionId $id_coleccion): ?Coleccion;
	
    public function getNewId();
}