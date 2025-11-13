<?php

namespace src\utils_database\application\repositories;

use PDO;
use src\utils_database\domain\entity\MapId;
use src\utils_database\domain\contracts\MapIdRepositoryInterface;
use src\utils_database\infrastructure\repositories\PgMapIdRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo MapId
 * 
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 13/11/2025
 */
class MapIdRepository implements MapIdRepositoryInterface
{

    /**$
     * @var MapIdRepositoryInterface
     */
    private MapIdRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgMapIdRepository();
    }

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo MapId
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo MapId
	
	 */
	public function getMapIdes(array $aWhere=[], array $aOperators=[]): array|FALSE
	{
	    return $this->repository->getMapIdes($aWhere, $aOperators);
	}
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(MapId $MapId): bool
    {
        return $this->repository->Eliminar($MapId);
    }

	public function Guardar(MapId $MapId): bool
    {
        return $this->repository->Guardar($MapId);
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
     * @param string $objeto
     * @return array|bool
	
     */
    public function datosById(string $objeto, int $id_resto): array|bool
    {
        return $this->repository->datosById($objeto, $id_resto);
    }
	
    /**
     * Busca la clase con objeto en el repositorio.
	
     */
    public function findById(string $objeto, int $id_resto): ?MapId
    {
        return $this->repository->findById($objeto, $id_resto);
    }
}