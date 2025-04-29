<?php

namespace src\menus\application\repositories;

use PDO;
use src\menus\domain\entity\GrupMenuRole;
use src\menus\domain\contracts\GrupMenuRoleRepositoryInterface;
use src\menus\infrastructure\repositories\PgGrupMenuRoleRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo GrupMenuRole
 * 
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/4/2025
 */
class GrupMenuRoleRepository implements GrupMenuRoleRepositoryInterface
{

    /**$
     * @var GrupMenuRoleRepositoryInterface
     */
    private GrupMenuRoleRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgGrupMenuRoleRepository();
    }

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo GrupMenuRole
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo GrupMenuRole
	
	 */
	public function getGrupMenuRoles(array $aWhere=[], array $aOperators=[]): array|FALSE
	{
	    return $this->repository->getGrupMenuRoles($aWhere, $aOperators);
	}
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(GrupMenuRole $GrupMenuRole): bool
    {
        return $this->repository->Eliminar($GrupMenuRole);
    }

	public function Guardar(GrupMenuRole $GrupMenuRole): bool
    {
        return $this->repository->Guardar($GrupMenuRole);
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
    public function findById(int $id_item): ?GrupMenuRole
    {
        return $this->repository->findById($id_item);
    }
	
    public function getNewId()
    {
        return $this->repository->getNewId();
    }
}