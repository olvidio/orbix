<?php

namespace src\menus\application\repositories;

use PDO;
use src\menus\domain\entity\TemplateMenu;
use src\menus\domain\contracts\TemplateMenuRepositoryInterface;
use src\menus\infrastructure\repositories\PgTemplateMenuRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo TemplateMenu
 * 
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 15/4/2025
 */
class TemplateMenuRepository implements TemplateMenuRepositoryInterface
{

    /**$
     * @var TemplateMenuRepositoryInterface
     */
    private TemplateMenuRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgTemplateMenuRepository();
    }

    public function getArrayTemplates(): array
    {
       return $this->repository->getArrayTemplates();
    }

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo TemplateMenu
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo TemplateMenu
	
	 */
	public function getTemplatesMenus(array $aWhere=[], array $aOperators=[]): array|FALSE
	{
	    return $this->repository->getTemplatesMenus($aWhere, $aOperators);
	}
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(TemplateMenu $TemplateMenu): bool
    {
        return $this->repository->Eliminar($TemplateMenu);
    }

	public function Guardar(TemplateMenu $TemplateMenu): bool
    {
        return $this->repository->Guardar($TemplateMenu);
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
     * @param int $id_template_menu
     * @return array|bool
	
     */
    public function datosById(int $id_template_menu): array|bool
    {
        return $this->repository->datosById($id_template_menu);
    }
	
    /**
     * Busca la clase con id_template_menu en el repositorio.
	
     */
    public function findById(int $id_template_menu): ?TemplateMenu
    {
        return $this->repository->findById($id_template_menu);
    }

    public function findByName(string $nombre): ?TemplateMenu
    {
        return $this->repository->findByName($nombre);
    }

    public function getNewId()
    {
        return $this->repository->getNewId();
    }

}