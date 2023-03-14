<?php

namespace misas\domain\entity;
/**
 * Clase que implementa la entidad misa_plantillas_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 13/3/2023
 */
class Plantilla {

	/* ATRIBUTOS ----------------------------------------------------------------- */

	/**
	 * Id_item de Plantilla
	 *
	 * @var int
	 */
	 private int $iid_item;
	/**
	 * Id_ctr de Plantilla
	 *
	 * @var int
	 */
	 private int $iid_ctr;
	/**
	 * Que de Plantilla
	 *
	 * @var int
	 */
	 private int $ique;
	/**
	 * Dia de Plantilla
	 *
	 * @var int
	 */
	 private int $idia;
	/**
	 * Semana de Plantilla
	 *
	 * @var int|null
	 */
	 private int|null $isemana = null;
	/**
	 * Id_nom de Plantilla
	 *
	 * @var int|null
	 */
	 private int|null $iid_nom = null;
	/**
	 * T_start de Plantilla
	 *
	 * @var string time
	 */
	 private string time $tt_start;
	/**
	 * T_end de Plantilla
	 *
	 * @var string time
	 */
	 private string time $tt_end;
	/**
	 * Observ de Plantilla
	 *
	 * @var string|null
	 */
	 private string|null $sobserv = null;

	/* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

	/**
	 * Establece el valor de todos los atributos
	 *
	 * @param array $aDatos
	 * @return Plantilla
	 */
	public function setAllAttributes(array $aDatos): Plantilla
	{
		if (array_key_exists('id_item',$aDatos))
		{
			$this->setId_item($aDatos['id_item']);
		}
		if (array_key_exists('id_ctr',$aDatos))
		{
			$this->setId_ctr($aDatos['id_ctr']);
		}
		if (array_key_exists('que',$aDatos))
		{
			$this->setQue($aDatos['que']);
		}
		if (array_key_exists('dia',$aDatos))
		{
			$this->setDia($aDatos['dia']);
		}
		if (array_key_exists('semana',$aDatos))
		{
			$this->setSemana($aDatos['semana']);
		}
		if (array_key_exists('id_nom',$aDatos))
		{
			$this->setId_nom($aDatos['id_nom']);
		}
		if (array_key_exists('t_start',$aDatos))
		{
			$this->setT_start($aDatos['t_start']);
		}
		if (array_key_exists('t_end',$aDatos))
		{
			$this->setT_end($aDatos['t_end']);
		}
		if (array_key_exists('observ',$aDatos))
		{
			$this->setObserv($aDatos['observ']);
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
	 * @return int $iid_ctr
	 */
	public function getId_ctr(): int
	{
		return $this->iid_ctr;
	}
	/**
	 *
	 * @param int $iid_ctr
	 */
	public function setId_ctr(int $iid_ctr): void
	{
		$this->iid_ctr = $iid_ctr;
	}
	/**
	 *
	 * @return int $ique
	 */
	public function getQue(): int
	{
		return $this->ique;
	}
	/**
	 *
	 * @param int $ique
	 */
	public function setQue(int $ique): void
	{
		$this->ique = $ique;
	}
	/**
	 *
	 * @return int $idia
	 */
	public function getDia(): int
	{
		return $this->idia;
	}
	/**
	 *
	 * @param int $idia
	 */
	public function setDia(int $idia): void
	{
		$this->idia = $idia;
	}
	/**
	 *
	 * @return int|null $isemana
	 */
	public function getSemana(): ?int
	{
		return $this->isemana;
	}
	/**
	 *
	 * @param int|null $isemana
	 */
	public function setSemana(?int $isemana = null): void
	{
		$this->isemana = $isemana;
	}
	/**
	 *
	 * @return int|null $iid_nom
	 */
	public function getId_nom(): ?int
	{
		return $this->iid_nom;
	}
	/**
	 *
	 * @param int|null $iid_nom
	 */
	public function setId_nom(?int $iid_nom = null): void
	{
		$this->iid_nom = $iid_nom;
	}
	/**
	 *
	 * @return string time $tt_start
	 */
	public function getT_start(): string time
	{
		return $this->tt_start;
	}
	/**
	 *
	 * @param string time $tt_start
	 */
	public function setT_start(string time $tt_start): void
	{
		$this->tt_start = $tt_start;
	}
	/**
	 *
	 * @return string time $tt_end
	 */
	public function getT_end(): string time
	{
		return $this->tt_end;
	}
	/**
	 *
	 * @param string time $tt_end
	 */
	public function setT_end(string time $tt_end): void
	{
		$this->tt_end = $tt_end;
	}
	/**
	 *
	 * @return string|null $sobserv
	 */
	public function getObserv(): ?string
	{
		return $this->sobserv;
	}
	/**
	 *
	 * @param string|null $sobserv
	 */
	public function setObserv(?string $sobserv = null): void
	{
		$this->sobserv = $sobserv;
	}
}