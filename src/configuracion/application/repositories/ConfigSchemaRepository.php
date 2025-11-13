<?php

namespace src\configuracion\application\repositories;

use PDO;
use src\configuracion\domain\entity\ConfigSchema;
use src\configuracion\domain\contracts\ConfigSchemaRepositoryInterface;
use src\configuracion\infrastructure\repositories\PgConfigSchemaRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo ConfigSchema
 * 
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/11/2025
 */
class ConfigSchemaRepository implements ConfigSchemaRepositoryInterface
{

    /**$
     * @var ConfigSchemaRepositoryInterface
     */
    private ConfigSchemaRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgConfigSchemaRepository();
    }

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo ConfigSchema
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo ConfigSchema
	
	 */
	public function getConfigSchemas(array $aWhere=[], array $aOperators=[]): array|FALSE
	{
	    return $this->repository->getConfigSchemas($aWhere, $aOperators);
	}
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(ConfigSchema $ConfigSchema): bool
    {
        return $this->repository->Eliminar($ConfigSchema);
    }

	public function Guardar(ConfigSchema $ConfigSchema): bool
    {
        return $this->repository->Guardar($ConfigSchema);
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
     * @param string $parametro
     * @return array|bool
	
     */
    public function datosById(string $parametro): array|bool
    {
        return $this->repository->datosById($parametro);
    }
	
    /**
     * Busca la clase con parametro en el repositorio.
	
     */
    public function findById(string $parametro): ?ConfigSchema
    {
        return $this->repository->findById($parametro);
    }
}