<?php

namespace src\usuarios\domain\entity;

use function core\is_true;
use src\usuarios\domain\value_objects\IdLocale;
use src\usuarios\domain\value_objects\NombreLocale;
use src\usuarios\domain\value_objects\Idioma;
use src\usuarios\domain\value_objects\NombreIdioma;

/**
 * Clase que implementa la entidad x_locales
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/4/2025
 */
class Local {

	/* ATRIBUTOS ----------------------------------------------------------------- */

	/**
	 * Id_locale de Local
	 *
	 * @var IdLocale
	 */
	 private IdLocale $sid_locale;
	/**
	 * Nom_locale de Local
	 *
	 * @var NombreLocale|null
	 */
	 private ?NombreLocale $snom_locale = null;
	/**
	 * Idioma de Local
	 *
	 * @var Idioma|null
	 */
	 private ?Idioma $sidioma = null;
	/**
	 * Nom_idioma de Local
	 *
	 * @var NombreIdioma|null
	 */
	 private ?NombreIdioma $snom_idioma = null;
	/**
	 * Activo de Local
	 *
	 * @var bool
	 */
	 private bool $bactivo;

	/* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

	/**
	 * Establece el valor de todos los atributos
	 *
	 * @param array $aDatos
	 * @return Local
	 */
	public function setAllAttributes(array $aDatos): Local
	{
		if (array_key_exists('id_locale',$aDatos))
		{
			// Check if it's already an IdLocale object
			if ($aDatos['id_locale'] instanceof IdLocale) {
				$this->setId_locale($aDatos['id_locale']);
			} else {
				$this->setId_locale(new IdLocale($aDatos['id_locale']));
			}
		}
		if (array_key_exists('nom_locale',$aDatos))
		{
			if ($aDatos['nom_locale'] === null) {
				$this->setNom_locale(null);
			} else {
				// Check if it's already a NombreLocale object
				if ($aDatos['nom_locale'] instanceof NombreLocale) {
					$this->setNom_locale($aDatos['nom_locale']);
				} else {
					$this->setNom_locale(new NombreLocale($aDatos['nom_locale']));
				}
			}
		}
		if (array_key_exists('idioma',$aDatos))
		{
			if ($aDatos['idioma'] === null) {
				$this->setIdioma(null);
			} else {
				// Check if it's already an Idioma object
				if ($aDatos['idioma'] instanceof Idioma) {
					$this->setIdioma($aDatos['idioma']);
				} else {
					$this->setIdioma(new Idioma($aDatos['idioma']));
				}
			}
		}
		if (array_key_exists('nom_idioma',$aDatos))
		{
			if ($aDatos['nom_idioma'] === null) {
				$this->setNom_idioma(null);
			} else {
				// Check if it's already a NombreIdioma object
				if ($aDatos['nom_idioma'] instanceof NombreIdioma) {
					$this->setNom_idioma($aDatos['nom_idioma']);
				} else {
					$this->setNom_idioma(new NombreIdioma($aDatos['nom_idioma']));
				}
			}
		}
		if (array_key_exists('activo',$aDatos))
		{
			$this->setActivo(is_true($aDatos['activo']));
		}
		return $this;
	}
	/**
	 *
	 * @return IdLocale
	 */
	public function getId_locale(): IdLocale
	{
		return $this->sid_locale;
	}
	/**
	 *
	 * @param IdLocale $sid_locale
	 */
	public function setId_locale(IdLocale $sid_locale): void
	{
		$this->sid_locale = $sid_locale;
	}

	/**
	 * Get the locale identifier as a string
	 *
	 * @return string
	 */
	public function getId_localeAsString(): string
	{
		return $this->sid_locale->value();
	}
	/**
	 *
	 * @return NombreLocale|null
	 */
	public function getNom_locale(): ?NombreLocale
	{
		return $this->snom_locale;
	}
	/**
	 *
	 * @param NombreLocale|null $snom_locale
	 */
	public function setNom_locale(?NombreLocale $snom_locale = null): void
	{
		$this->snom_locale = $snom_locale;
	}

	/**
	 * Get the locale name as a string
	 *
	 * @return string|null
	 */
	public function getNom_localeAsString(): ?string
	{
		return $this->snom_locale ? $this->snom_locale->value() : null;
	}
	/**
	 *
	 * @return Idioma|null
	 */
	public function getIdioma(): ?Idioma
	{
		return $this->sidioma;
	}
	/**
	 *
	 * @param Idioma|null $sidioma
	 */
	public function setIdioma(?Idioma $sidioma = null): void
	{
		$this->sidioma = $sidioma;
	}

	/**
	 * Get the language as a string
	 *
	 * @return string|null
	 */
	public function getIdiomaAsString(): ?string
	{
		return $this->sidioma ? $this->sidioma->value() : null;
	}
	/**
	 *
	 * @return NombreIdioma|null
	 */
	public function getNom_idioma(): ?NombreIdioma
	{
		return $this->snom_idioma;
	}
	/**
	 *
	 * @param NombreIdioma|null $snom_idioma
	 */
	public function setNom_idioma(?NombreIdioma $snom_idioma = null): void
	{
		$this->snom_idioma = $snom_idioma;
	}

	/**
	 * Get the language name as a string
	 *
	 * @return string|null
	 */
	public function getNom_idiomaAsString(): ?string
	{
		return $this->snom_idioma ? $this->snom_idioma->value() : null;
	}
	/**
	 *
	 * @return bool $bactivo
	 */
	public function isActivo(): bool
	{
		return $this->bactivo;
	}
	/**
	 *
	 * @param bool $bactivo
	 */
	public function setActivo(bool $bactivo): void
	{
		$this->bactivo = $bactivo;
	}
}
