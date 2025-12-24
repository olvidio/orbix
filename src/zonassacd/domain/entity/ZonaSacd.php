<?php

namespace src\zonassacd\domain\entity;
	use function core\is_true;

    /**
 * Clase que implementa la entidad zonas_sacd
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/12/2025
 */
class ZonaSacd {

	/* ATRIBUTOS ----------------------------------------------------------------- */

	/**
	 * Id_item de ZonaSacd
	 *
	 * @var int
	 */
	 private int $iid_item;
	/**
	 * Id_nom de ZonaSacd
	 *
	 * @var int
	 */
	 private int $iid_nom;
	/**
	 * Id_zona de ZonaSacd
	 *
	 * @var int
	 */
	 private int $iid_zona;
	/**
	 * Propia de ZonaSacd
	 *
	 * @var bool
	 */
	 private bool $bpropia;
	/**
	 * Dw1 de ZonaSacd
	 *
	 * @var bool|null
	 */
	 private bool|null $bdw1 = null;
	/**
	 * Dw2 de ZonaSacd
	 *
	 * @var bool|null
	 */
	 private bool|null $bdw2 = null;
	/**
	 * Dw3 de ZonaSacd
	 *
	 * @var bool|null
	 */
	 private bool|null $bdw3 = null;
	/**
	 * Dw4 de ZonaSacd
	 *
	 * @var bool|null
	 */
	 private bool|null $bdw4 = null;
	/**
	 * Dw5 de ZonaSacd
	 *
	 * @var bool|null
	 */
	 private bool|null $bdw5 = null;
	/**
	 * Dw6 de ZonaSacd
	 *
	 * @var bool|null
	 */
	 private bool|null $bdw6 = null;
	/**
	 * Dw7 de ZonaSacd
	 *
	 * @var bool|null
	 */
	 private bool|null $bdw7 = null;

	/* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

	/**
	 * Establece el valor de todos los atributos
	 *
	 * @param array $aDatos
	 * @return ZonaSacd
	 */
	public function setAllAttributes(array $aDatos): ZonaSacd
	{
		if (array_key_exists('id_item',$aDatos))
		{
			$this->setId_item($aDatos['id_item']);
		}
		if (array_key_exists('id_nom',$aDatos))
		{
			$this->setId_nom($aDatos['id_nom']);
		}
		if (array_key_exists('id_zona',$aDatos))
		{
			$this->setId_zona($aDatos['id_zona']);
		}
		if (array_key_exists('propia',$aDatos))
		{
			$this->setPropia(is_true($aDatos['propia']));
		}
		if (array_key_exists('dw1',$aDatos))
		{
			$this->setDw1(is_true($aDatos['dw1']));
		}
		if (array_key_exists('dw2',$aDatos))
		{
			$this->setDw2(is_true($aDatos['dw2']));
		}
		if (array_key_exists('dw3',$aDatos))
		{
			$this->setDw3(is_true($aDatos['dw3']));
		}
		if (array_key_exists('dw4',$aDatos))
		{
			$this->setDw4(is_true($aDatos['dw4']));
		}
		if (array_key_exists('dw5',$aDatos))
		{
			$this->setDw5(is_true($aDatos['dw5']));
		}
		if (array_key_exists('dw6',$aDatos))
		{
			$this->setDw6(is_true($aDatos['dw6']));
		}
		if (array_key_exists('dw7',$aDatos))
		{
			$this->setDw7(is_true($aDatos['dw7']));
		}
		return $this;
	}
	/**
	 *
	 * @return int $iid_item
	 */
	public function getId_item(): int
	{
		return $this->iid_item;
	}
	/**
	 *
	 * @param int $iid_item
	 */
	public function setId_item(int $iid_item): void
	{
		$this->iid_item = $iid_item;
	}
	/**
	 *
	 * @return int $iid_nom
	 */
	public function getId_nom(): int
	{
		return $this->iid_nom;
	}
	/**
	 *
	 * @param int $iid_nom
	 */
	public function setId_nom(int $iid_nom): void
	{
		$this->iid_nom = $iid_nom;
	}
	/**
	 *
	 * @return int $iid_zona
	 */
	public function getId_zona(): int
	{
		return $this->iid_zona;
	}
	/**
	 *
	 * @param int $iid_zona
	 */
	public function setId_zona(int $iid_zona): void
	{
		$this->iid_zona = $iid_zona;
	}
	/**
	 *
	 * @return bool $bpropia
	 */
	public function isPropia(): bool
	{
		return $this->bpropia;
	}
	/**
	 *
	 * @param bool $bpropia
	 */
	public function setPropia(bool $bpropia): void
	{
		$this->bpropia = $bpropia;
	}
	/**
	 *
	 * @return bool|null $bdw1
	 */
	public function isDw1(): ?bool
	{
		return $this->bdw1;
	}
	/**
	 *
	 * @param bool|null $bdw1
	 */
	public function setDw1(?bool $bdw1 = null): void
	{
		$this->bdw1 = $bdw1;
	}
	/**
	 *
	 * @return bool|null $bdw2
	 */
	public function isDw2(): ?bool
	{
		return $this->bdw2;
	}
	/**
	 *
	 * @param bool|null $bdw2
	 */
	public function setDw2(?bool $bdw2 = null): void
	{
		$this->bdw2 = $bdw2;
	}
	/**
	 *
	 * @return bool|null $bdw3
	 */
	public function isDw3(): ?bool
	{
		return $this->bdw3;
	}
	/**
	 *
	 * @param bool|null $bdw3
	 */
	public function setDw3(?bool $bdw3 = null): void
	{
		$this->bdw3 = $bdw3;
	}
	/**
	 *
	 * @return bool|null $bdw4
	 */
	public function isDw4(): ?bool
	{
		return $this->bdw4;
	}
	/**
	 *
	 * @param bool|null $bdw4
	 */
	public function setDw4(?bool $bdw4 = null): void
	{
		$this->bdw4 = $bdw4;
	}
	/**
	 *
	 * @return bool|null $bdw5
	 */
	public function isDw5(): ?bool
	{
		return $this->bdw5;
	}
	/**
	 *
	 * @param bool|null $bdw5
	 */
	public function setDw5(?bool $bdw5 = null): void
	{
		$this->bdw5 = $bdw5;
	}
	/**
	 *
	 * @return bool|null $bdw6
	 */
	public function isDw6(): ?bool
	{
		return $this->bdw6;
	}
	/**
	 *
	 * @param bool|null $bdw6
	 */
	public function setDw6(?bool $bdw6 = null): void
	{
		$this->bdw6 = $bdw6;
	}
	/**
	 *
	 * @return bool|null $bdw7
	 */
	public function isDw7(): ?bool
	{
		return $this->bdw7;
	}
	/**
	 *
	 * @param bool|null $bdw7
	 */
	public function setDw7(?bool $bdw7 = null): void
	{
		$this->bdw7 = $bdw7;
	}
}