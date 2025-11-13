<?php

namespace src\utils_database\application\repositories;

use PDO;
use src\utils_database\domain\entity\DbSchema;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;
use src\utils_database\infrastructure\repositories\PgDbSchemaRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo DbSchema
 * 
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 13/11/2025
 */
class DbSchemaRepository implements DbSchemaRepositoryInterface
{

    /**$
     * @var DbSchemaRepositoryInterface
     */
    private DbSchemaRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgDbSchemaRepository();
    }

/* -------------------- GESTOR BASE ---------------------------------------- */

    public function cambiarNombre($old, $new, $database): void
    {
        $this->repository->cambiarNombre($old, $new, $database);
    }

    public function llenarNuevo($schema, $database): void
    {
        $this->repository->llenarNuevo($schema, $database);
    }

	/**
	 * devuelve una colección (array) de objetos de tipo DbSchema
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo DbSchema
	
	 */
	public function getDbSchemas(array $aWhere=[], array $aOperators=[]): array|FALSE
	{
	    return $this->repository->getDbSchemas($aWhere, $aOperators);
	}
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(DbSchema $DbSchema): bool
    {
        return $this->repository->Eliminar($DbSchema);
    }

	public function Guardar(DbSchema $DbSchema): bool
    {
        return $this->repository->Guardar($DbSchema);
    }

	public function getErrorTxt(): string
    {
        return $this->repository->getErrorTxt();
    }

	public function getoDbl(): PDO
    {
        return $this->repository->getoDbl();
    }

	public function setoDbl(PDO $oDbl): void
    {
        $this->repository->setoDbl($oDbl);
    }

	public function getNomTabla(): string
    {
        return $this->repository->getNomTabla();
    }
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param string $schema
     * @return array|bool
	
     */
    public function datosById(string $schema): array|bool
    {
        return $this->repository->datosById($schema);
    }
	
    /**
     * Busca la clase con schema en el repositorio.
	
     */
    public function findById(string $schema): ?DbSchema
    {
        return $this->repository->findById($schema);
    }
}