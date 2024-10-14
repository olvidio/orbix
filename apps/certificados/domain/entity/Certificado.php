<?php

namespace certificados\domain\entity;
	use core\ConfigGlobal;
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
	 private $iid_item;
	/**
	 * Id_nom de Certificado
	 *
	 * @var int|null
	 */
	 private $iid_nom = null;
    /**
     * Nombre de Certificado
     *
     * @var string|null
     */
    private $snom = null;
    /**
     * idioma de Certificado
     *
     * @var string|null
     */
    private $sidioma = null;
    /**
     * destino de Certificado
     *
     * @var string|null
     */
    private $sdestino = null;
	/**
	 * Certificado de Certificado
	 *
	 * @var string|null
	 */
	 private $scertificado = null;
	/**
	 * F_certificado de Certificado
	 *
	 * @var DateTimeLocal|null
	 */
	 private $df_certificado = null;
	/**
	 * Esquema emisor de Certificado
	 *
	 * @var string|null
	 */
	 private $sesquema_emisor = null;
	/**
	 * firmado de Certificado
	 *
	 * @var bool|null
	 */
	 private $bfirmado = null;
	/**
	 * Documento de Certificado
	 *
	 * @var string|null
	 */
	 private $sdocumento = null;
    /**
     * F_enviado de Certificado
     *
     * @var DateTimeLocal|null
     */
    private $df_enviado = null;

	/* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

	/**
	 * Establece el valor de todos los atributos
	 *
	 * @param array $aDatos
	 * @return Certificado
	 */
	public function setAllAttributes(array $aDatos)
	{
		if (array_key_exists('id_item',$aDatos))
		{
			$this->setId_item($aDatos['id_item']);
		}
		if (array_key_exists('id_nom',$aDatos))
		{
			$this->setId_nom($aDatos['id_nom']);
		}
        if (array_key_exists('nom',$aDatos))
        {
            $this->setNom($aDatos['nom']);
        }
        if (array_key_exists('idioma',$aDatos))
        {
            $this->setIdioma($aDatos['idioma']);
        }
        if (array_key_exists('destino',$aDatos))
        {
            $this->setDestino($aDatos['destino']);
        }
		if (array_key_exists('certificado',$aDatos))
		{
			$this->setCertificado($aDatos['certificado']);
		}
		if (array_key_exists('f_certificado',$aDatos))
		{
			$this->setF_certificado($aDatos['f_certificado']);
		}
		if (array_key_exists('esquema_emisor',$aDatos))
		{
			$this->setEsquema_emisor(is_true($aDatos['esquema_emisor']));
		}
		if (array_key_exists('firmado',$aDatos))
		{
			$this->setFirmado(is_true($aDatos['firmado']));
		}
		if (array_key_exists('documento',$aDatos))
		{
			$this->setDocumento($aDatos['documento']);
		}
        if (array_key_exists('f_enviado',$aDatos))
        {
            $this->setF_enviado($aDatos['f_enviado']);
        }
		return $this;
	}
	/**
	 *
	 * @return int $iid_item
	 */
	public function getId_item()
	{
		return $this->iid_item;
	}
	/**
	 *
	 * @param int $iid_item
	 */
	public function setId_item(int $iid_item)
	{
		$this->iid_item = $iid_item;
	}
	/**
	 *
	 * @return int|null $iid_nom
	 */
	public function getId_nom()
	{
		return $this->iid_nom;
	}
	/**
	 *
	 * @param int|null $iid_nom
	 */
	public function setId_nom(?int $iid_nom = null)
	{
		$this->iid_nom = $iid_nom;
	}

    /**
     * @return string|null
     */
    public function getNom(): ?string
    {
        return $this->snom;
    }

    /**
     * @param string|null $nom
     */
    public function setNom(?string $nom): void
    {
        $this->snom = $nom;
    }

    /**
     * @return string|null
     */
    public function getIdioma(): ?string
    {
        return $this->sidioma;
    }

    /**
     * @param string|null $idioma
     */
    public function setIdioma(?string $idioma): void
    {
        $this->sidioma = $idioma;
    }

    /**
     * @return string|null
     */
    public function getDestino(): ?string
    {
        return $this->sdestino;
    }

    /**
     * @param string|null $destino
     */
    public function setDestino(?string $destino): void
    {
        $this->sdestino = $destino;
    }

	/**
	 *
	 * @return string|null $scertificado
	 */
	public function getCertificado()
	{
		return $this->scertificado;
	}
	/**
	 *
	 * @param string|null $scertificado
	 */
	public function setCertificado(?string $scertificado = null)
	{
		$this->scertificado = $scertificado;
	}
	/**
	 *
	 * @return DateTimeLocal|NullDateTimeLocal|null $df_certificado
	 */
	public function getF_certificado()
	{
        return $this->df_certificado?? new NullDateTimeLocal;
	}
	/**
	 * 
	 * @param DateTimeLocal|null $df_certificado
	 */
	public function setF_certificado($df_certificado = null)
	{
        $this->df_certificado = $df_certificado;
	}
    /**
     *
     * @return bool
     */
    public function isPropio()
    {
        return ($this->sesquema_emisor === ConfigGlobal::mi_region_dl());
    }
	/**
	 *
	 * @return ?string $sesquema_emisor
	 */
	public function getEsquema_emisor()
	{
		return $this->sesquema_emisor;
	}
	/**
	 *
	 * @param ?string $sesquema_emisor
	 */
	public function setEsquema_emisor(string $sesquema_emisor=null)
	{
		$this->sesquema_emisor = $sesquema_emisor;
	}
	/**
	 *
	 * @return bool|null $bfirmado
	 */
	public function isFirmado()
	{
		return $this->bfirmado;
	}
	/**
	 *
	 * @param bool|null $bfirmado
	 */
	public function setFirmado(?bool $bfirmado = null)
	{
		$this->bfirmado = $bfirmado;
	}
	/**
	 *
	 * @return string|null $sdocumento
	 */
	public function getDocumento()
	{
		return $this->sdocumento;
	}
	/**
	 *
	 * @param string|null $sdocumento
	 */
	public function setDocumento(?string $sdocumento = null)
	{
		$this->sdocumento = $sdocumento;
	}
    /**
     *
     * @return DateTimeLocal|NullDateTimeLocal|null $df_enviado
     */
    public function getF_enviado()
    {
        return $this->df_enviado?? new NullDateTimeLocal;
    }
    /**
     *
     * @param DateTimeLocal|null $df_enviado
     */
    public function setF_enviado($df_enviado = null)
    {
        $this->df_enviado = $df_enviado;
    }
}