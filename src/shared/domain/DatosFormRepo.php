<?php

namespace src\shared\domain;

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


    private $camposForm;
    private $camposNo;

    private $oFicha;
    private array $aOpciones_txt;
    private $mod = '';

    public function getFormularioData()
    {
        $camposForm = '';
        $camposNo = '';
        $formData = [];

        $oFicha = $this->getFicha();
        foreach ($oFicha->getDatosCampos() as $oDatosCampo) {
            //$tabla=$oDatosCampo->getNom_tabla();	// Para usarlo a la hora de comprobar los campos.
            $metodo = $oDatosCampo->getMetodoGet();
            $nom_camp = $oDatosCampo->getNom_camp();
            $camposForm .= empty($camposForm) ? $nom_camp : '!' . $nom_camp;
            if ($this->mod === 'nuevo') {
                $valor_camp = '';
            } else if (substr($metodo,-2) === 'Vo') {
                $valor_camp = $oFicha->$metodo()->value();
            } else {
                $valor_camp = $oFicha->$metodo();
            }
            $var_1 = $oDatosCampo->getArgument();
            $eti = $oDatosCampo->getEtiqueta();
            $tipo = $oDatosCampo->getTipo();

            $field = [
                'tipo' => $tipo,
                'nombre' => $nom_camp,
                'etiqueta' => $eti,
                'valor' => $valor_camp,
                'argument' => $var_1
            ];

            switch ($tipo) {
                case "ver":
                    if ($this->mod !== 'nuevo') {
                        // No additional data needed for "ver" type
                    }
                    $camposNo .= empty($camposNo) ? $nom_camp : '!' . $nom_camp;
                    break;
                case "decimal":
                case "texto":
                    $field['size'] = $var_1;
                    break;
                case "fecha":
                    $locale_us = ConfigGlobal::is_locale_us();
                    if (!empty($valor_camp)) {
                        // el valor_camp debe ser un objeto DateTimeLocal
                        $field['valor_txt'] = $valor_camp->getFromLocal();
                    } else {
                        $field['valor_txt'] = $valor_camp;
                    }
                    $field['locale_us'] = $locale_us;
                    break;
                case "opciones":
                    $acc = $oDatosCampo->getAccion();
                    $var_3 = $oDatosCampo->getArgument3();

                    $RepoRelacionado = new $var_1();
                    $a_opciones = $RepoRelacionado->$var_3();

                    $field['accion'] = $acc;
                    $field['opciones'] = $a_opciones;
                    $field['aOpcion_no'] = $this->aOpcion_no ?? [];
                    break;
                case "depende":
                    $field['opciones_txt'] = $this->aOpciones_txt[$nom_camp] ?? '';
                    break;
                case "array":
                    $aOpciones = $oDatosCampo->getLista();
                    $oDesplegable = new Desplegable($nom_camp, $aOpciones, $valor_camp, true);
                    $field['options_html'] = $oDesplegable->options();
                    break;
                case "check":
                    $field['checked'] = is_true($valor_camp);
                    //los check a falso no se pueden comprobar.
                    $camposNo .= empty($camposNo) ? $nom_camp : '!' . $nom_camp;
                    break;
            }

            $formData[] = $field;
        }
        $this->camposForm = $camposForm;
        $this->camposNo = $camposNo;

        return [
            'fields' => $formData,
            'camposForm' => $camposForm,
            'camposNo' => $camposNo
        ];
    }

    public function getFormulario()
    {
        // For backward compatibility, generate HTML from the data
        $formData = $this->getFormularioData();
        $formulario = '';

        foreach ($formData['fields'] as $field) {
            $tipo = $field['tipo'];
            $nom_camp = $field['nombre'];
            $eti = $field['etiqueta'];
            $valor_camp = $field['valor'];

            switch ($tipo) {
                case "ver":
                    if ($this->mod !== 'nuevo') {
                        $formulario .= "<tr><td class=etiqueta>" . ucfirst($eti) . "</td>";
                        $formulario .= "<td class=contenido>" . htmlspecialchars($valor_camp ?? '') . "</td></tr>";
                        $formulario .= "<input type='hidden' name='$nom_camp' value=\"" . htmlspecialchars($valor_camp ?? '') . "\"></td></tr>";
                    }
                    break;
                case "decimal":
                case "texto":
                    $formulario .= "<tr><td class=etiqueta>" . ucfirst($eti) . "</td>";
                    $size = $field['size'];
                    $formulario .= "<td class=contenido><input type='text' name='$nom_camp' value=\"" . htmlspecialchars($valor_camp ?? '') . "\" size='$size'></td></tr>";
                    break;
                case "fecha":
                    $formulario .= "<tr><td class=etiqueta>" . ucfirst($eti) . "</td>";
                    $locale_us = $field['locale_us'];
                    $valor_camp_txt = $field['valor_txt'];
                    $formulario .= "<td class=contenido><input class=\"fecha\" type=\"text\" id=\"$nom_camp\" name=\"$nom_camp\" value=\"$valor_camp_txt\" 
									onchange='fnjs_comprobar_fecha(\"#$nom_camp\",$locale_us)'>";
                    break;
                case "opciones":
                    $formulario .= "<tr><td class=etiqueta>" . ucfirst($eti) . "</td>";
                    $acc = $field['accion'];
                    $a_opciones = $field['opciones'];
                    $aOpcion_no = $field['aOpcion_no'];

                    $accion = empty($acc) ? '' : "onchange=\"fnjs_actualizar_depende('$nom_camp','$acc');\" ";
                    $formulario .= "<td class=contenido><select id=\"$nom_camp\" name=\"$nom_camp\" $accion>";
                    $formulario .= "<option></option>";
                    foreach ($a_opciones as $key => $val) {
                        if ((string)$key === (string)$valor_camp) {
                            $sel = 'selected';
                        } else {
                            $sel = '';
                        }
                        if (!empty($aOpcion_no) && is_array($aOpcion_no) && in_array($key, $aOpcion_no)) continue;
                        $formulario .= "<option value=\"$key\" $sel>$val</option>";
                    }
                    $formulario .= "</select></td></tr>";
                    break;
                case "depende":
                    $formulario .= "<tr><td class=etiqueta>" . ucfirst($eti) . "</td>";
                    $formulario .= "<td class=contenido><select id=\"$nom_camp\" name=\"$nom_camp\">";
                    $formulario .= $field['opciones_txt'];  // solo útil en el caso de nuevo. En el resto se actualiza desde el campo del que depende.
                    $formulario .= "</select></td></tr>";
                    break;
                case "array":
                    $formulario .= "<tr><td class=etiqueta>" . ucfirst($eti) . "</td>";
                    $formulario .= "<td class=contenido><select name=\"$nom_camp\">";
                    $formulario .= $field['options_html'];
                    $formulario .= "</select></td></tr>";
                    break;
                case "check":
                    $formulario .= "<tr><td class=etiqueta>" . ucfirst($eti) . "</td>";
                    $chk = $field['checked'] ? "checked" : "";
                    $formulario .= "<td class=contenido><input type='checkbox' name='$nom_camp' $chk>";
                    break;
            }
        }

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
