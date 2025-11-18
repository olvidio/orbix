<?php

namespace src\actividades\application\repositories;

use PDO;
use src\actividades\domain\entity\NivelStgr;
use src\actividades\domain\contracts\NivelStgrRepositoryInterface;
use src\actividades\infrastructure\repositories\PgNivelStgrRepository;
use src\actividades\domain\value_objects\NivelStgrId;


/**
 *
 * Clase para gestionar la lista de objetos tipo NivelStgr
 * 
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 18/11/2025
 */
class NivelStgrRepository implements NivelStgrRepositoryInterface
{

    /**$
     * @var NivelStgrRepositoryInterface
     */
    private NivelStgrRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgNivelStgrRepository();
    }

    public function getArrayNivelesStgr(): array
    {
        return $this->repository->getArrayNivelesStgr();
    }

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo NivelStgr
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo NivelStgr
	
	 */
	public function getNivelesStgr(array $aWhere=[], array $aOperators=[]): array|FALSE
	{
	    return $this->repository->getNivelesStgr($aWhere, $aOperators);
	}
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(NivelStgr $NivelStgr): bool
    {
        return $this->repository->Eliminar($NivelStgr);
    }

	public function Guardar(NivelStgr $NivelStgr): bool
    {
        return $this->repository->Guardar($NivelStgr);
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
     * @param int $nivel_stgr
     * @return array|bool
	
     */
    public function datosById(int $nivel_stgr): array|bool
    {
        return $this->repository->datosById($nivel_stgr);
    }
    
    public function datosByIdVO(NivelStgrId $id): array|bool
    {
        return $this->repository->datosByIdVO($id);
    }
	
    /**
     * Busca la clase con nivel_stgr en el repositorio.
	
     */
    public function findById(int $nivel_stgr): ?NivelStgr
    {
        return $this->repository->findById($nivel_stgr);
    }
    
    public function findByIdVO(NivelStgrId $id): ?NivelStgr
    {
        return $this->repository->findByIdVO($id);
    }
	
    public function getNewId()
    {
        return $this->repository->getNewId();
    }
    
    public function getNewIdVO(): NivelStgrId
    {
        return $this->repository->getNewIdVO();
    }
}