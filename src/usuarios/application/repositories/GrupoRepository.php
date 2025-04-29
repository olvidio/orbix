<?php

namespace src\usuarios\application\repositories;

use PDO;
use src\usuarios\domain\entity\Grupo;
use src\usuarios\domain\contracts\GrupoRepositoryInterface;
use src\usuarios\infrastructure\repositories\PgGrupoRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo Grupo
 * 
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/4/2025
 */
class GrupoRepository implements GrupoRepositoryInterface
{

    /**$
     * @var GrupoRepositoryInterface
     */
    private GrupoRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgGrupoRepository();
    }

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo Grupo
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo Grupo
	
	 */
	public function getGrupos(array $aWhere=[], array $aOperators=[]): array|FALSE
	{
	    return $this->repository->getGrupos($aWhere, $aOperators);
	}
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Grupo $Grupo): bool
    {
        return $this->repository->Eliminar($Grupo);
    }

	public function Guardar(Grupo $Grupo): bool
    {
        return $this->repository->Guardar($Grupo);
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
     * @param int $id_usuario
     * @return array|bool
	
     */
    public function datosById(int $id_usuario): array|bool
    {
        return $this->repository->datosById($id_usuario);
    }
	
    /**
     * Busca la clase con id_usuario en el repositorio.
	
     */
    public function findById(int $id_usuario): ?Grupo
    {
        return $this->repository->findById($id_usuario);
    }
	
    public function getNewId()
    {
        return $this->repository->getNewId();
    }
}