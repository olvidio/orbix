<?php

namespace src\usuarios\domain\contracts;

use PDO;
use src\usuarios\domain\entity\UsuarioGrupo;


/**
 * Interfaz de la clase UsuarioGrupo y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/4/2025
 */
interface UsuarioGrupoRepositoryInterface
{

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo UsuarioGrupo
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo UsuarioGrupo
	
	 */
	public function getUsuariosGrupos(array $aWhere=[], array $aOperators=[]): array|FALSE;
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(UsuarioGrupo $UsuarioGrupo): bool;

	public function Guardar(UsuarioGrupo $UsuarioGrupo): bool;

	public function getErrorTxt(): string;

	public function getoDbl(): PDO;

	public function setoDbl(PDO $oDbl): void;

	public function getNomTabla(): string;
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param int $id_usuario
     * @return array|bool
	
     */
    public function datosById(int $id_usuario): array|bool;
	
    /**
     * Busca la clase con id_usuario en el repositorio.
	
     */
    public function findById(int $id_usuario): ?UsuarioGrupo;
}