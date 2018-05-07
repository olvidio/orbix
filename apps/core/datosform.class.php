<?php
namespace core;
use core;
use web;

/**
 * Classe que implementa l'entitat d_dossiers_abiertos
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 09/04/2018
 */
class DatosForm {
	/* ATRIBUTS ----------------------------------------------------------------- */
	

	private $formulario;
	private $camposForm;
	private $camposNo;

	private $oFicha;
	private $despl_depende;

	public function getFormulario() {
		$camposForm = '';
		$camposNo = '';
		$formulario = '';

		$oFicha = $this->getFicha();
		$oFicha->DBCarregar();
		$clasname = get_class($oFicha);
		foreach ($oFicha->getDatosCampos() as $oDatosCampo) {
			$tabla=$oDatosCampo->getNom_tabla();	// Para usarlo a la hora de comprobar los campos.
			$nom_camp=$oDatosCampo->getNom_camp();	
			$camposForm .= empty($camposForm)? $nom_camp : '!'.$nom_camp; 
			$valor_camp=$oFicha->$nom_camp;	
			$var_1=$oDatosCampo->getArgument();
			$eti=$oDatosCampo->getEtiqueta();
			$formulario.="<tr><td class=etiqueta>".ucfirst($eti)."</td>";
			switch($oDatosCampo->getTipo()) {
				case "ver":
					if ($Qmod == 'nuevo') { // si es nuevo lo muestro como texto
						$size= isset($var_1)? $var_1 : '';
						$formulario.="<td class=contenido><input type='text' name='$nom_camp' value=\"".htmlspecialchars($valor_camp)."\" size='$size'></td></tr>";
					} else {
						$formulario.="<td class=contenido>".htmlspecialchars($valor_camp)."</td></tr>";
						$formulario.="<input type='hidden' name='$nom_camp' value=\"".htmlspecialchars($valor_camp)."\"></td></tr>";
					}
					break;
				case "decimal":
				case "texto":
					$size=$var_1;
					$formulario.="<td class=contenido><input type='text' name='$nom_camp' value=\"".htmlspecialchars($valor_camp)."\" size='$size'></td></tr>";
					break;
				case "fecha":
					$formulario.="<td class=contenido><input class='fecha' type='text' id='$nom_camp' name='$nom_camp' value='$valor_camp' 
									onchange='fnjs_comprobar_fecha(\"#$nom_camp\")'>";	
					break;
				case "opciones":
					$acc=$oDatosCampo->getAccion();
					$var_3=$oDatosCampo->getArgument3();
					$gestor=preg_replace('/\\\(\w*)$/', '\Gestor\1', $var_1);
					$oRelacionado = new $gestor();
					$oDesplegable=$oRelacionado->$var_3();
					$oDesplegable->setOpcion_sel($valor_camp);

					$accion = empty($acc)? '' : "onchange=\"fnjs_actualizar_depende('$nom_camp','$acc');\" ";
					$formulario.="<td class=contenido><select id=\"$nom_camp\" name=\"$nom_camp\" $accion>";
					$formulario.= $oDesplegable->options();
					$formulario.="</select></td></tr>";
					break;
				case "depende":
					$nom_despl= "despl_".$nom_camp;
					$formulario.="<td class=contenido><select id=\"$nom_camp\" name=\"$nom_camp\">";
					$formulario.= $this->despl_depende;  // solo útil en el caso de nuevo. En el resto se actualiza desde el campo del que depende.
					//$formulario.= $$nom_despl; // solo útil en el caso de nuevo. En el resto se actualiza desde el campo del que depende.
					$formulario.="</select></td></tr>";
					break;
				case "array":
//					$oDespl = new web\Desplegable();
//					$oDespl->setOpciones($var_1);
//					$oDespl->setOpcion_sel($valor_camp);
					$aOpciones=$oDatosCampo->getLista();
					$oDesplegable=new web\Desplegable($nom_camp,$aOpciones,$valor_camp,true);
					$formulario.="<td class=contenido><select name=\"$nom_camp\">";
					$formulario.= $oDesplegable->options();
					$formulario.="</select></td></tr>";
					break;
				case "check":
					if ($valor_camp=="t") { $chk="checked"; } else { $chk=""; }
					$formulario.="<td class=contenido><input type='checkbox' name='$nom_camp' $chk>";
					//los check a falso no se pueden comprobar.
					$camposNo .= empty($camposNo)? $nom_camp : '!'.$nom_camp; 
					break;
			}
		}
		$this->camposForm = $camposForm;
		$this->camposNo = $camposNo;

		return $formulario;
	}
	
	public function getCamposForm() {
		if (!isset($this->camposForm)) {
			$this->getFormulario();
		}
		return $this->camposForm;
	}

	public function getCamposNo() {
		if (!isset($this->camposNo)) {
			$this->getFormulario();
		}
		return $this->camposNo;
	}

	public function setFicha($oFicha) {
		$this->oFicha = $oFicha;
	}

	public function getFicha() {
		return $this->oFicha;
	}
	
	public function setDespl_depende($despl_depende) {
		$this->despl_depende = $despl_depende;
	}
}