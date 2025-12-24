<?php

namespace src\menus\domain\entity;
use core\DatosCampo;
use core\Set;
use src\menus\domain\value_objects\TemplateMenuName;

/**
 * Clase que implementa la entidad aux_templates_menus
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 15/4/2025
 */
class TemplateMenu {

	/* ATRIBUTOS ----------------------------------------------------------------- */

	/**
	 * Id_template_menu de TemplateMenu
	 *
	 * @var int
	 */
	 private int $iid_template_menu;
	/**
	 * Nombre de TemplateMenu
	 *
	 * @var string|null
	 */
	 private string|null $snombre = null;

	/* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

	/**
	 * Establece el valor de todos los atributos
	 *
	 * @param array $aDatos
	 * @return TemplateMenu
	 */
	public function setAllAttributes(array $aDatos): TemplateMenu
	{
		if (array_key_exists('id_template_menu',$aDatos))
		{
			$this->setId_template_menu($aDatos['id_template_menu']);
		}
		if (array_key_exists('nombre',$aDatos))
		{
			$this->setNombre($aDatos['nombre']);
		}
		return $this;
	}
	/**
	 *
	 * @return int $iid_template_menu
	 */
	public function getId_template_menu(): int
	{
		return $this->iid_template_menu;
	}
	/**
	 *
	 * @param int $iid_template_menu
	 */
	public function setId_template_menu(int $iid_template_menu): void
	{
		$this->iid_template_menu = $iid_template_menu;
	}
	/**
	 *
	 * @return string|null $snombre
	 */
	public function getNombre(): ?string
	{
		return $this->snombre;
	}
	/**
	 *
	 * @param string|null $snombre
	 */
	public function setNombre(string|TemplateMenuName|null $snombre = null): void
	{
		$this->snombre = $snombre instanceof TemplateMenuName ? $snombre->value() : $snombre;
	}

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key()
    {
        return 'id_template_menu';
    }

    function getDatosCampos()
    {
        $oSet = new Set();

        $oSet->add($this->getDatosNombre());
        return $oSet->getTot();
    }

    function getDatosNombre()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('nombre');
        $oDatosCampo->setMetodoGet('getNombre');
        $oDatosCampo->setMetodoSet('setNombre');
        $oDatosCampo->setEtiqueta(_("nombre plantilla menú"));
        $oDatosCampo->setTipo('texto');
        return $oDatosCampo;
    }
}