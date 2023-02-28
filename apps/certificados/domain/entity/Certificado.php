<?php

namespace certificados\domain\entity;
	use function core\is_true;
	use web\DateTimeLocal;
	use web\NullDateTimeLocal;
/**
 * Clase que implementa la entidad e_certificados_rstgr
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 27/2/2023
 */
class Certificado {

	/* ATRIBUTOS ----------------------------------------------------------------- */

	/**
	 * Id_item de Certificado
	 *
	 * @var int
	 */
	 private int $iid_item;
	/**
	 * Id_nom de Certificado
	 *
	 * @var int|null
	 */
	 private int|null $iid_nom = null;
	/**
	 * Certificado de Certificado
	 *
	 * @var string|null
	 */
	 private string|null $scertificado = null;
	/**
	 * F_certificado de Certificado
	 *
	 * @var DateTimeLocal|null
	 */
	 private DateTimeLocal|null $df_certificado = null;
	/**
	 * Propio de Certificado
	 *
	 * @var bool|null
	 */
	 private bool|null $bpropio = null;
	/**
	 * Copia de Certificado
	 *
	 * @var bool|null
	 */
	 private bool|null $bcopia = null;
	/**
	 * Documento de Certificado
	 *
	 * @var string|null
	 */
	 private string|null $sdocumento = null;

	/* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

	/**
	 * Establece el valor de todos los atributos
	 *
	 * @param array $aDatos
	 * @return Certificado
	 */
	public function setAllAttributes(array $aDatos): Certificado
	{
		if (array_key_exists('id_item',$aDatos))
		{
			$this->setId_item($aDatos['id_item']);
		}
		if (array_key_exists('id_nom',$aDatos))
		{
			$this->setId_nom($aDatos['id_nom']);
		}
		if (array_key_exists('certificado',$aDatos))
		{
			$this->setCertificado($aDatos['certificado']);
		}
		if (array_key_exists('f_certificado',$aDatos))
		{
			$this->setF_certificado($aDatos['f_certificado']);
		}
		if (array_key_exists('propio',$aDatos))
		{
			$this->setPropio(is_true($aDatos['propio']));
		}
		if (array_key_exists('copia',$aDatos))
		{
			$this->setCopia(is_true($aDatos['copia']));
		}
		if (array_key_exists('documento',$aDatos))
		{
			$this->setDocumento($aDatos['documento']);
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
	 * @return string|null $scertificado
	 */
	public function getCertificado(): ?string
	{
		return $this->scertificado;
	}
	/**
	 *
	 * @param string|null $scertificado
	 */
	public function setCertificado(?string $scertificado = null): void
	{
		$this->scertificado = $scertificado;
	}
	/**
	 *
	 * @return DateTimeLocal|NullDateTimeLocal|null $df_certificado
	 */
	public function getF_certificado(): DateTimeLocal|NullDateTimeLocal|null
	{
        return $this->df_certificado?? new NullDateTimeLocal;
	}
	/**
	 * 
	 * @param DateTimeLocal|null $df_certificado
	 */
	public function setF_certificado(DateTimeLocal|null $df_certificado = null): void
	{
        $this->df_certificado = $df_certificado;
	}
	/**
	 *
	 * @return bool|null $bpropio
	 */
	public function isPropio(): ?bool
	{
		return $this->bpropio;
	}
	/**
	 *
	 * @param bool|null $bpropio
	 */
	public function setPropio(?bool $bpropio = null): void
	{
		$this->bpropio = $bpropio;
	}
	/**
	 *
	 * @return bool|null $bcopia
	 */
	public function isCopia(): ?bool
	{
		return $this->bcopia;
	}
	/**
	 *
	 * @param bool|null $bcopia
	 */
	public function setCopia(?bool $bcopia = null): void
	{
		$this->bcopia = $bcopia;
	}
	/**
	 *
	 * @return string|null $sdocumento
	 */
	public function getDocumento(): ?string
	{
		return $this->sdocumento;
	}
	/**
	 *
	 * @param string|null $sdocumento
	 */
	public function setDocumento(?string $sdocumento = null): void
	{
		$this->sdocumento = $sdocumento;
	}
}