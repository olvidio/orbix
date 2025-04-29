<?php

namespace src\menus\application\repositories;

use PDO;
use src\menus\domain\entity\MenuDb;
use src\menus\domain\contracts\MenuDbRepositoryInterface;
use src\menus\infrastructure\repositories\PgMenuDbRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo MenuDb
 * 
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 15/4/2025
 */
class MenuDbRepository implements MenuDbRepositoryInterface
{

    /**$
     * @var MenuDbRepositoryInterface
     */
    private MenuDbRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgMenuDbRepository();
    }

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo MenuDb
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo MenuDb
	
	 */
	public function getMenuDbs(array $aWhere=[], array $aOperators=[]): array|FALSE
	{
	    return $this->repository->getMenuDbs($aWhere, $aOperators);
	}
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(MenuDb $MenuDb): bool
    {
        return $this->repository->Eliminar($MenuDb);
    }

	public function Guardar(MenuDb $MenuDb): bool
    {
        return $this->repository->Guardar($MenuDb);
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
     * @param int $id_menu
     * @return array|bool
	
     */
    public function datosById(int $id_menu): array|bool
    {
        return $this->repository->datosById($id_menu);
    }
	
    /**
     * Busca la clase con id_menu en el repositorio.
	
     */
    public function findById(int $id_menu): ?MenuDb
    {
        return $this->repository->findById($id_menu);
    }
	
    public function getNewId()
    {
        return $this->repository->getNewId();
    }
}