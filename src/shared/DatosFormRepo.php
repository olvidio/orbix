<?php

namespace src\shared;

use core\ConfigGlobal;
use web\Desplegable;
use function core\is_true;

/**
 * Clase que implementa la entidad d_dossiers_abiertos
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 09/04/2018
 */
class DatosFormRepo
{
    /* ATRIBUTOS ----------------------------------------------------------------- */


    private $formulario;
    private $camposForm;
    private $camposNo;

    private $oFicha;
    private array $aOpciones_txt;
    private $mod = '';

    public function getFormulario()
    {
        $camposForm = '';
        $camposNo = '';
        $formulario = '';

        $oFicha = $this->getFicha();
        foreach ($oFicha->getDatosCampos() as $oDatosCampo) {
            //$tabla=$oDatosCampo->getNom_tabla();	// Para usarlo a la hora de comprobar los campos.
            $metodo = $oDatosCampo->getMetodoGet();
            $nom_camp = $oDatosCampo->getNom_camp();
            $camposForm .= empty($camposForm) ? $nom_camp : '!' . $nom_camp;
            if ($this->mod === 'nuevo') {
                $valor_camp = '';
            } else {
                $valor_camp = $oFicha->$metodo();
            }
            $var_1 = $oDatosCampo->getArgument();
            $eti = $oDatosCampo->getEtiqueta();
            switch ($oDatosCampo->getTipo()) {
                case "ver":
                    if ($this->mod !== 'nuevo') {
                        $formulario .= "<tr><td class=etiqueta>" . ucfirst($eti) . "</td>";
                        $formulario .= "<td class=contenido>" . htmlspecialchars($valor_camp ?? '') . "</td></tr>";
                        $formulario .= "<input type='hidden' name='$nom_camp' value=\"" . htmlspecialchars($valor_camp ?? '') . "\"></td></tr>";
                    }
                    $camposNo .= empty($camposNo) ? $nom_camp : '!' . $nom_camp;
                    break;
                case "decimal":
                case "texto":
                    $formulario .= "<tr><td class=etiqueta>" . ucfirst($eti) . "</td>";
                    $size = $var_1;
                    $formulario .= "<td class=contenido><input type='text' name='$nom_camp' value=\"" . htmlspecialchars($valor_camp ?? '') . "\" size='$size'></td></tr>";
                    break;
                case "fecha":
                    $formulario .= "<tr><td class=etiqueta>" . ucfirst($eti) . "</td>";
                    $locale_us = ConfigGlobal::is_locale_us();
                    // el valor_camp debe ser un objeto DateTimeLocal
                    $valor_camp_txt = $valor_camp->getFromLocal();
                    $formulario .= "<td class=contenido><input class=\"fecha\" type=\"text\" id=\"$nom_camp\" name=\"$nom_camp\" value=\"$valor_camp_txt\" 
									onchange='fnjs_comprobar_fecha(\"#$nom_camp\",$locale_us)'>";
                    break;
                case "opciones":
                    $formulario .= "<tr><td class=etiqueta>" . ucfirst($eti) . "</td>";
                    $acc = $oDatosCampo->getAccion();
                    $var_3 = $oDatosCampo->getArgument3();

                    $RepoRelacionado = new $var_1();
                    $a_opciones = $RepoRelacionado->$var_3();

                    $accion = empty($acc) ? '' : "onchange=\"fnjs_actualizar_depende('$nom_camp','$acc');\" ";
                    $formulario .= "<td class=contenido><select id=\"$nom_camp\" name=\"$nom_camp\" $accion>";
                    $formulario .= "<option></option>";
                    foreach ($a_opciones as $key => $val) {
                        if ((string)$key === (string)$valor_camp) {
                            $sel = 'selected';
                        } else {
                            $sel = '';
                        }
                        if (!empty($this->aOpcion_no) && is_array($this->aOpcion_no) && in_array($key, $this->aOpcion_no)) continue;
                        $formulario .= "<option value=\"$key\" $sel>$val</option>";
                    }
                    $formulario .= "</select></td></tr>";
                    break;
                case "depende":
                    $formulario .= "<tr><td class=etiqueta>" . ucfirst($eti) . "</td>";
                    $formulario .= "<td class=contenido><select id=\"$nom_camp\" name=\"$nom_camp\">";
                    $formulario .= $this->aOpciones_txt[$nom_camp];  // solo útil en el caso de nuevo. En el resto se actualiza desde el campo del que depende.
                    $formulario .= "</select></td></tr>";
                    break;
                case "array":
                    $formulario .= "<tr><td class=etiqueta>" . ucfirst($eti) . "</td>";
//					$oDespl = new Desplegable();
//					$oDespl->setOpciones($var_1);
//					$oDespl->setOpcion_sel($valor_camp);
                    $aOpciones = $oDatosCampo->getLista();
                    $oDesplegable = new Desplegable($nom_camp, $aOpciones, $valor_camp, true);
                    $formulario .= "<td class=contenido><select name=\"$nom_camp\">";
                    $formulario .= $oDesplegable->options();
                    $formulario .= "</select></td></tr>";
                    break;
                case "check":
                    $formulario .= "<tr><td class=etiqueta>" . ucfirst($eti) . "</td>";
                    if (is_true($valor_camp)) {
                        $chk = "checked";
                    } else {
                        $chk = "";
                    }
                    $formulario .= "<td class=contenido><input type='checkbox' name='$nom_camp' $chk>";
                    //los check a falso no se pueden comprobar.
                    $camposNo .= empty($camposNo) ? $nom_camp : '!' . $nom_camp;
                    break;
            }
        }
        $this->camposForm = $camposForm;
        $this->camposNo = $camposNo;

        return $formulario;
    }

    public function getCamposForm()
    {
        if (!isset($this->camposForm)) {
            $camposForm = '';
            $oFicha = $this->getFicha();
            foreach ($oFicha->getDatosCampos() as $oDatosCampo) {
                $nom_camp = $oDatosCampo->getNom_camp();
                $camposForm .= empty($camposForm) ? $nom_camp : '!' . $nom_camp;
            }
            $this->camposForm = $camposForm;
        }
        return $this->camposForm;
    }

    public function getCamposNo()
    {
        if (!isset($this->camposNo)) {
            $camposNo = '';
            $oFicha = $this->getFicha();
            foreach ($oFicha->getDatosCampos() as $oDatosCampo) {
                $nom_camp = $oDatosCampo->getNom_camp();
                switch ($oDatosCampo->getTipo()) {
                    case "ver":
                        $camposNo .= empty($camposNo) ? $nom_camp : '!' . $nom_camp;
                        break;
                    case "check":
                        //los check a falso no se pueden comprobar.
                        $camposNo .= empty($camposNo) ? $nom_camp : '!' . $nom_camp;
                        break;
                }
            }
            $this->camposNo = $camposNo;
        }
        return $this->camposNo;
    }

    public function setFicha($oFicha)
    {
        $this->oFicha = $oFicha;
    }

    public function getFicha()
    {
        return $this->oFicha;
    }

    public function setArrayOpcionesTxt($aOpciones_txt)
    {
        $this->aOpciones_txt = $aOpciones_txt;
    }

    /**
     * @return mixed
     */
    public function getMod()
    {
        return $this->mod;
    }

    /**
     * @param mixed $mod
     */
    public function setMod(mixed $mod): void
    {
        $this->mod = $mod;
    }
}