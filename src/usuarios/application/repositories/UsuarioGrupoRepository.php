<?php

namespace src\usuarios\application\repositories;

use PDO;
use src\usuarios\domain\entity\UsuarioGrupo;
use src\usuarios\domain\contracts\UsuarioGrupoRepositoryInterface;
use src\usuarios\infrastructure\repositories\PgUsuarioGrupoRepository;


/**
 *
 * Clase para gestionar la lista de objetos tipo UsuarioGrupo
 * 
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/4/2025
 */
class UsuarioGrupoRepository implements UsuarioGrupoRepositoryInterface
{

    /**$
     * @var UsuarioGrupoRepositoryInterface
     */
    private UsuarioGrupoRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new PgUsuarioGrupoRepository();
    }

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo UsuarioGrupo
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo UsuarioGrupo
	
	 */
	public function getUsuariosGrupos(array $aWhere=[], array $aOperators=[]): array|FALSE
	{
	    return $this->repository->getUsuariosGrupos($aWhere, $aOperators);
	}
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(UsuarioGrupo $UsuarioGrupo): bool
    {
        return $this->repository->Eliminar($UsuarioGrupo);
    }

	public function Guardar(UsuarioGrupo $UsuarioGrupo): bool
    {
        return $this->repository->Guardar($UsuarioGrupo);
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

    public function setoDbl_Select(PDO $oDbl): void
    {
        $this->repository->setoDbl_Select($oDbl);
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
    public function findById(int $id_usuario): ?UsuarioGrupo
    {
        return $this->repository->findById($id_usuario);
    }
}