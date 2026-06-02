<?php

namespace src\utils_database\domain\contracts;

use src\utils_database\domain\entity\DbSchema;

/**
 * Interfaz de la clase DbSchema y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 13/11/2025
 */
interface DbSchemaRepositoryInterface
{

    public function cambiarNombre($old, $new, $database): void;

    public function llenarNuevo($schema, $database): void;

/* --------------------  BASiC SEARCH ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo DbSchema
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array Una colección de objetos de tipo DbSchema
	
	 */
	public function getDbSchemas(array $aWhere=[], array $aOperators=[]): array;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(DbSchema $DbSchema): bool;

	public function Guardar(DbSchema $DbSchema): bool;

	public function getErrorTxt(): string;



	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param string $schema
     * @return array|bool
	
     */
    public function datosById(string $schema): array|bool;
	
    /** Siguiente valor de `id` para insertar filas (usa MAX(id)+1 en `db_idschema`). */
    public function getNewId(): int;

    /**
     * Busca la clase con schema en el repositorio.
	
     */
    public function findById(string $schema): ?DbSchema;
}