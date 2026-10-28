<?php

namespace src\configuracion\domain\contracts;

use src\configuracion\domain\entity\ConfigSchema;
use src\shared\domain\criteria\Criteria;

/**
 * Interfaz de la clase ConfigSchema y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/11/2025
 */
interface ConfigSchemaRepositoryInterface
{

/* --------------------  BASiC SEARCH ---------------------------------------- */

	/**
	 * Parámetros de configuración del esquema que cumplen el criterio.
	 *
	 * @return list<ConfigSchema>
	 */
	public function matching(Criteria $criteria): array;

	/**
	 * devuelve una colección (array) de objetos de tipo ConfigSchema
	 *
	 * @deprecated usar matching(Criteria)
	 * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return list<ConfigSchema> Una colección de objetos de tipo ConfigSchema
	
	 */
	public function getConfigSchemas(array $aWhere=[], array $aOperators=[]): array;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(ConfigSchema $ConfigSchema): bool;

	public function Guardar(ConfigSchema $ConfigSchema): bool;

	public function getErrorTxt(): string;



	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param string $parametro
     * @return array<string, mixed>|false
	
     */
    public function datosById(string $parametro): array|false;
	
    /**
     * Busca la clase con parametro en el repositorio.
	
     */
    public function findById(string $parametro): ?ConfigSchema;
}