<?php

namespace src\actividades\application\repositories;

use PDO;
use src\actividades\domain\entity\Repeticion;
use src\actividades\domain\contracts\RepeticionRepositoryInterface;
use src\actividades\infrastructure\repositories\PgRepeticionRepository;
use src\actividades\domain\value_objects\RepeticionId;


/**
 *
 * Clase para gestionar la lista de objetos tipo Repeticion
 * 
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 18/11/2025
 */
class RepeticionRepository implements RepeticionRepositoryInterface
{

    /**$
     * @var RepeticionRepositoryInterface
     */
    private RepeticionRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgRepeticionRepository();
    }

    public function getArrayRepeticion(): array
    {
        return $this->repository->getArrayRepeticion();
    }

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo Repeticion
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo Repeticion
	
	 */
	public function getRepeticiones(array $aWhere=[], array $aOperators=[]): array|FALSE
	{
	    return $this->repository->getRepeticiones($aWhere, $aOperators);
	}
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Repeticion $Repeticion): bool
    {
        return $this->repository->Eliminar($Repeticion);
    }

	public function Guardar(Repeticion $Repeticion): bool
    {
        return $this->repository->Guardar($Repeticion);
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
     * @param int $id_repeticion
     * @return array|bool
	
     */
    public function datosById(int $id_repeticion): array|bool
    {
        return $this->repository->datosById($id_repeticion);
    }

    public function datosByIdVO(RepeticionId $id): array|bool
    {
        return $this->repository->datosByIdVO($id);
    }
	
    /**
     * Busca la clase con id_repeticion en el repositorio.
	
     */
    public function findById(int $id_repeticion): ?Repeticion
    {
        return $this->repository->findById($id_repeticion);
    }

    public function findByIdVO(RepeticionId $id): ?Repeticion
    {
        return $this->repository->findByIdVO($id);
    }
	
    public function getNewId()
    {
        return $this->repository->getNewId();
    }

    public function getNewIdVO(): RepeticionId
    {
        return $this->repository->getNewIdVO();
    }
}