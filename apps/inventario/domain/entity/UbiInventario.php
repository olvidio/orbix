<?php

namespace inventario\domain\entity;
use core\DatosCampo;
use core\Set;

/**
 * Clase que implementa la entidad i_ubis_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
class UbiInventario {

	/* ATRIBUTOS ----------------------------------------------------------------- */

	/**
	 * Id_ubi de UbiInventario
	 *
	 * @var int
	 */
	 private int $iid_ubi;
	/**
	 * Nom_ubi de UbiInventario
	 *
	 * @var string
	 */
	 private string $snom_ubi;
	/**
	 * Id_ubi_activ de UbiInventario
	 *
	 * @var int|null
	 */
	 private int|null $iid_ubi_activ = null;

	/* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

	/**
	 * Establece el valor de todos los atributos
	 *
	 * @param array $aDatos
	 * @return UbiInventario
	 */
	public function setAllAttributes(array $aDatos): UbiInventario
	{
		if (array_key_exists('id_ubi',$aDatos))
		{
			$this->setId_ubi($aDatos['id_ubi']);
		}
		if (array_key_exists('nom_ubi',$aDatos))
		{
			$this->setNom_ubi($aDatos['nom_ubi']);
		}
		if (array_key_exists('id_ubi_activ',$aDatos))
		{
			$this->setId_ubi_activ($aDatos['id_ubi_activ']);
		}
		return $this;
	}
	/**
	 *
	 * @return int $iid_ubi
	 */
	public function getId_ubi(): int
	{
		return $this->iid_ubi;
	}
	/**
	 *
	 * @param int $iid_ubi
	 */
	public function setId_ubi(int $iid_ubi): void
	{
		$this->iid_ubi = $iid_ubi;
	}
	/**
	 *
	 * @return string $snom_ubi
	 */
	public function getNom_ubi(): string
	{
		return $this->snom_ubi;
	}
	/**
	 *
	 * @param string $snom_ubi
	 */
	public function setNom_ubi(string $snom_ubi): void
	{
		$this->snom_ubi = $snom_ubi;
	}
	/**
	 *
	 * @return int|null $iid_ubi_activ
	 */
	public function getId_ubi_activ(): ?int
	{
		return $this->iid_ubi_activ;
	}
	/**
	 *
	 * @param int|null $iid_ubi_activ
	 */
	public function setId_ubi_activ(?int $iid_ubi_activ = null): void
	{
		$this->iid_ubi_activ = $iid_ubi_activ;
	}

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key()
    {
        return 'id_ubi';
    }

    function getDatosCampos()
    {
        $oSet = new Set();

        $oSet->add($this->getDatosNom_ubi());
        return $oSet->getTot();
    }

    function getDatosNom_ubi()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('nom_ubi');
        $oDatosCampo->setMetodoGet('getNom_ubi');
        $oDatosCampo->setMetodoSet('setNom_ubi');
        $oDatosCampo->setEtiqueta(_("nombre del centro/casa"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;

    }
}