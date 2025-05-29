<?php

namespace src\inventario\application\repositories;

use PDO;
use src\inventario\domain\contracts\EgmRepositoryInterface;
use src\inventario\domain\entity\Egm;
use src\inventario\infrastructure\repositories\PgEgmRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo Egm
 * 
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
class EgmRepository implements EgmRepositoryInterface
{

    /**$
     * @var EgmRepositoryInterface
     */
    private EgmRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgEgmRepository();
    }


    public function getUltimoGrupo(int $id_equipaje): int
    {
        return $this->repository->getUltimoGrupo($id_equipaje);
    }

    public function getArrayIdFromIdEquipajes($aEquipajes, $lugar = ''): array
    {
        return $this->repository->getArrayIdFromIdEquipajes($aEquipajes, $lugar);
    }

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo Egm
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo Egm
	
	 */
	public function getEgmes(array $aWhere=[], array $aOperators=[]): array|FALSE
	{
	    return $this->repository->getEgmes($aWhere, $aOperators);
	}
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Egm $Egm): bool
    {
        return $this->repository->Eliminar($Egm);
    }

	public function Guardar(Egm $Egm): bool
    {
        return $this->repository->Guardar($Egm);
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
     * @param int $id_item
     * @return array|bool
	
     */
    public function datosById(int $id_item): array|bool
    {
        return $this->repository->datosById($id_item);
    }
	
    /**
     * Busca la clase con id_item en el repositorio.
	
     */
    public function findById(int $id_item): ?Egm
    {
        return $this->repository->findById($id_item);
    }
	
    public function getNewId()
    {
        return $this->repository->getNewId();
    }

}