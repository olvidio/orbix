<?php

namespace src\menus\application\repositories;

use PDO;
use src\menus\domain\entity\GrupMenu;
use src\menus\domain\contracts\GrupMenuRepositoryInterface;
use src\menus\infrastructure\repositories\PgGrupMenuRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo GrupMenu
 * 
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 15/4/2025
 */
class GrupMenuRepository implements GrupMenuRepositoryInterface
{

    /**$
     * @var GrupMenuRepositoryInterface
     */
    private GrupMenuRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgGrupMenuRepository();
    }

    public function getArrayGrupMenus(): array
    {
        return $this->repository->getArrayGrupMenus();
    }

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo GrupMenu
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo GrupMenu
	
	 */
	public function getGrupMenus(array $aWhere=[], array $aOperators=[]): array|FALSE
	{
	    return $this->repository->getGrupMenus($aWhere, $aOperators);
	}
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(GrupMenu $GrupMenu): bool
    {
        return $this->repository->Eliminar($GrupMenu);
    }

	public function Guardar(GrupMenu $GrupMenu): bool
    {
        return $this->repository->Guardar($GrupMenu);
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
     * @param int $id_grupmenu
     * @return array|bool
	
     */
    public function datosById(int $id_grupmenu): array|bool
    {
        return $this->repository->datosById($id_grupmenu);
    }
	
    /**
     * Busca la clase con id_grupmenu en el repositorio.
	
     */
    public function findById(int $id_grupmenu): ?GrupMenu
    {
        return $this->repository->findById($id_grupmenu);
    }
	
    public function getNewId()
    {
        return $this->repository->getNewId();
    }
}