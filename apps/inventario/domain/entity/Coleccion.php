<?php

namespace inventario\domain\entity;
	use core\DatosCampo;
    use core\Set;
    use function core\is_true;
/**
 * Clase que implementa la entidad i_colecciones_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
class Coleccion {

	/* ATRIBUTOS ----------------------------------------------------------------- */

	/**
	 * Id_coleccion de Coleccion
	 *
	 * @var int
	 */
	 private int $iid_coleccion;
	/**
	 * Nom_coleccion de Coleccion
	 *
	 * @var string
	 */
	 private string $snom_coleccion;
	/**
	 * Agrupar de Coleccion
	 *
	 * @var bool|null
	 */
	 private bool|null $bagrupar = null;

	/* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

	/**
	 * Establece el valor de todos los atributos
	 *
	 * @param array $aDatos
	 * @return Coleccion
	 */
	public function setAllAttributes(array $aDatos): Coleccion
	{
		if (array_key_exists('id_coleccion',$aDatos))
		{
			$this->setId_coleccion($aDatos['id_coleccion']);
		}
		if (array_key_exists('nom_coleccion',$aDatos))
		{
			$this->setNom_coleccion($aDatos['nom_coleccion']);
		}
		if (array_key_exists('agrupar',$aDatos))
		{
			$this->setAgrupar(is_true($aDatos['agrupar']));
		}
		return $this;
	}
	/**
	 *
	 * @return int $iid_coleccion
	 */
	public function getId_coleccion(): int
	{
		return $this->iid_coleccion;
	}
	/**
	 *
	 * @param int $iid_coleccion
	 */
	public function setId_coleccion(int $iid_coleccion): void
	{
		$this->iid_coleccion = $iid_coleccion;
	}
	/**
	 *
	 * @return string $snom_coleccion
	 */
	public function getNom_coleccion(): string
	{
		return $this->snom_coleccion;
	}
	/**
	 *
	 * @param string $snom_coleccion
	 */
	public function setNom_coleccion(string $snom_coleccion): void
	{
		$this->snom_coleccion = $snom_coleccion;
	}
	/**
	 *
	 * @return bool|null $bagrupar
	 */
	public function isAgrupar(): ?bool
	{
		return $this->bagrupar;
	}
	/**
	 *
	 * @param bool|null $bagrupar
	 */
	public function setAgrupar(?bool $bagrupar = null): void
	{
		$this->bagrupar = $bagrupar;
	}

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key()
    {
        return 'id_coleccion';
    }

    function getDatosCampos()
    {
        $oSet = new Set();

        $oSet->add($this->getDatosNom_coleccion());
        $oSet->add($this->getDatosAgrupar());
        return $oSet->getTot();
    }

    function getDatosNom_coleccion()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('nom_coleccion');
        $oDatosCampo->setMetodoGet('getNom_coleccion');
        $oDatosCampo->setMetodoSet('setNom_coleccion');
        $oDatosCampo->setEtiqueta(_("nombre colección"));
        $oDatosCampo->setTipo('texto');
        return $oDatosCampo;
    }
    function getDatosAgrupar()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('agrupar');
        $oDatosCampo->setMetodoGet('isAgrupar');
        $oDatosCampo->setMetodoSet('setAgrupar');
        $oDatosCampo->setEtiqueta(_("agrupar"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }
}