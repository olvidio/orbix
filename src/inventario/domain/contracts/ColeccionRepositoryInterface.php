<?php

namespace src\inventario\domain\contracts;

use src\inventario\domain\entity\Coleccion;

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

    /**
     * @return array<int|string, string>
     */
    /**
     * @return array<int|string, string>
     */
    public function getArrayColecciones(): array;

/* --------------------  BASiC SEARCH ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo Coleccion
	 *
	 * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return list<Coleccion> Una colección de objetos de tipo Coleccion
	
	 */
	public function getColecciones(array $aWhere=[], array $aOperators=[]): array;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Coleccion $Coleccion): bool;

	public function Guardar(Coleccion $Coleccion): bool;

	public function getErrorTxt(): string;



	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param int $id_coleccion
     * @return array<string, mixed>|false
	
     */
    public function datosById(int $id_coleccion): array|false;
	
    /**
     * Busca la clase con id_coleccion en el repositorio.
	
     */
    public function findById(int $id_coleccion): ?Coleccion;
	
    public function getNewId(): int;
}