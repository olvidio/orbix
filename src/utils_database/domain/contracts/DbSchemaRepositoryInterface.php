<?php

namespace src\utils_database\domain\contracts;

use src\shared\domain\criteria\Criteria;
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

    public function cambiarNombre(string $old, string $new, string $database): void;

    public function llenarNuevo(string $schema, string $database): void;

/* --------------------  BASiC SEARCH ---------------------------------------- */

	/**
	 * DbSchema que cumplen el criterio.
	 *
	 * @return list<DbSchema>
	 */
	public function matching(Criteria $criteria): array;

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
     * @return array<string, mixed>|false
     */
    public function datosById(string $schema): array|false;
	
    /** Siguiente valor de `id` para insertar filas (usa MAX(id)+1 en `db_idschema`). */
    public function getNewId(): int;

    /**
     * Busca la clase con schema en el repositorio.
	
     */
    public function findById(string $schema): ?DbSchema;
}