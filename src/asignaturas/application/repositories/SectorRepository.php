<?php

namespace src\asignaturas\application\repositories;

use PDO;
use src\asignaturas\domain\entity\Sector;
use src\asignaturas\domain\contracts\SectorRepositoryInterface;
use src\asignaturas\infrastructure\repositories\PgSectorRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo Sector
 * 
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 14/11/2025
 */
class SectorRepository implements SectorRepositoryInterface
{

    /**$
     * @var SectorRepositoryInterface
     */
    private SectorRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgSectorRepository();
    }

    public function getArraySectores(): array
    {
        return $this->repository->getArraySectores();
    }

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo Sector
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo Sector
	
	 */
	public function getSectores(array $aWhere=[], array $aOperators=[]): array|FALSE
	{
	    return $this->repository->getSectores($aWhere, $aOperators);
	}
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Sector $Sector): bool
    {
        return $this->repository->Eliminar($Sector);
    }

	public function Guardar(Sector $Sector): bool
    {
        return $this->repository->Guardar($Sector);
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
     * @param int $id_sector
     * @return array|bool
	
     */
    public function datosById(int $id_sector): array|bool
    {
        return $this->repository->datosById($id_sector);
    }
	
    /**
     * Busca la clase con id_sector en el repositorio.
	
     */
    public function findById(int $id_sector): ?Sector
    {
        return $this->repository->findById($id_sector);
    }
	
    public function getNewId()
    {
        return $this->repository->getNewId();
    }
}