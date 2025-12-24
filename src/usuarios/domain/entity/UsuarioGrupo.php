<?php

namespace src\usuarios\domain\entity;
/**
 * Clase que implementa la entidad aux_cross_usuarios_grupos
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/4/2025
 */
class UsuarioGrupo {

	/* ATRIBUTOS ----------------------------------------------------------------- */

	/**
	 * Id_usuario de UsuarioGrupo
	 *
	 * @var int
	 */
	 private int $iid_usuario;
	/**
	 * Id_grupo de UsuarioGrupo
	 *
	 * @var int
	 */
	 private int $iid_grupo;

	/* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

	/**
	 * Establece el valor de todos los atributos
	 *
	 * @param array $aDatos
	 * @return UsuarioGrupo
	 */
	public function setAllAttributes(array $aDatos): UsuarioGrupo
	{
		if (array_key_exists('id_usuario',$aDatos))
		{
			$this->setId_usuario($aDatos['id_usuario']);
		}
		if (array_key_exists('id_grupo',$aDatos))
		{
			$this->setId_grupo($aDatos['id_grupo']);
		}
		return $this;
	}
	/**
	 *
	 * @return int $iid_usuario
	 */
	public function getId_usuario(): int
	{
		return $this->iid_usuario;
	}
	/**
	 *
	 * @param int $iid_usuario
	 */
	public function setId_usuario(int $iid_usuario): void
	{
		$this->iid_usuario = $iid_usuario;
	}
	/**
	 *
	 * @return int $iid_grupo
	 */
	public function getId_grupo(): int
	{
		return $this->iid_grupo;
	}
	/**
	 *
	 * @param int $iid_grupo
	 */
	public function setId_grupo(int $iid_grupo): void
	{
		$this->iid_grupo = $iid_grupo;
	}
}