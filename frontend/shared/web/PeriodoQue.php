<?php

namespace frontend\shared\web;

use frontend\shared\domain\value_objects\DateTimeLocal;

/**
 * Classe que presenta un quadre per establir un periode de cerques.
 */
class PeriodoQue
{
    private ?string $sTitulo = null;
    private ?Desplegable $oDesplPeriodos = null;
    private ?array $aOpcionesPeriodos = null;
    private ?Desplegable $oDesplAnys = null;
    private bool $isDesplAnysVisible = true;
    private ?array $aOpcionesAnys = null;
    private ?string $sBoton = null;
    private ?string $sAntes = null;
    private ?string $sEmpiezaMin = null;
    private ?string $sEmpiezaMax = null;
    private ?string $sFormName = null;
    private string $sAction = '';

    public function __construct()
    {
    }

    /**
     * Retorna una taula on poder triar un periode.
     */
    public function getTd(): string
    {
        if (empty($this->sFormName)) $this->sFormName = 'modifica';
        $sHtml = '<script>
			funjs_activar_fecha = function() {
				let f=$(\'#periodo\').val();
				if (f===\'otro\') {
					$(\'#span_fechas\').addClass(\'d_visible\');
					$(\'#span_fechas\').removeClass(\'d_novisible\');
				} else {
					$(\'#span_fechas\').addClass(\'d_novisible\');
					$(\'#span_fechas\').removeClass(\'d_visible\');
				}
			}
			$(function() { $( "#empiezamin" ).datepicker(); });
			$(function() { $( "#empiezamax" ).datepicker(); });
			$(document).ready(funjs_activar_fecha);
			</script>';

        if (isset($this->sAntes)) {
            $sHtml .= '<td>' . $this->sAntes . '</td>';
        }
        $sHtml .= '<td class=contenido>' . $this->getDesplPeriodos()->desplegable() . '</td>';
        if ($this->isDesplAnysVisible) {
            $sHtml .= '<td class=contenido>' . $this->getDesplAnys()->desplegable() . '</td>';
        } else {
            $sHtml .= '<input type="hidden" name="year" value="">';
        }
        $sHtml .= '<td colspan=5 id="span_fechas" class="d_novisible etiqueta" >   ';
        $sHtml .= _("entre");
        $sHtml .= '	<input type=text id="empiezamin" name="empiezamin" size="12" value="' . $this->sEmpiezaMin . '" class=fecha title="dd/mm/aa"  >';
        $sHtml .= '	' . _("y");
        $sHtml .= '	<input type="text" id="empiezamax" name="empiezamax" size="12" value="' . $this->sEmpiezaMax . '" class=fecha title="dd/mm/aa" >';
        $sHtml .= '</td>';
        if (isset($this->sBoton)) {
            $sHtml .= '<td>' . $this->sBoton . '</td>';
        }
        return $sHtml;
    }

    public function getHtml(): string
    {
        $sHtml = '<script>
			funjs_activar_fecha = function() {
				let f=$(\'#periodo\').val();
				if (f===\'otro\') {
					$(\'#span_fechas\').addClass(\'d_visible\');
					$(\'#span_fechas\').removeClass(\'d_novisible\');
				} else {
					$(\'#span_fechas\').addClass(\'d_novisible\');
					$(\'#span_fechas\').removeClass(\'d_visible\');
				}
			}
			$(function() { $( "#empiezamin" ).datepicker(); });
			$(function() { $( "#empiezamax" ).datepicker(); });
			$(document).ready(funjs_activar_fecha);
			</script>';

        $sHtml .= '<table>';
        $sHtml .= '<tr><th class=titulo_inv colspan="6">';
        $sHtml .= $this->sTitulo;
        $sHtml .= '</th></tr>';
        $sHtml .= '<tr><td>';
        $sHtml .= '<input type=hidden id=iasistentes_val name=iasistentes_val value="x">';
        $sHtml .= '<input type=hidden id=iactividad_val name=iactividad_val value="x">';
        $sHtml .= '</td>';
        if (isset($this->sAntes)) {
            $sHtml .= '<td>' . $this->sAntes . '</td>';
        }
        if ($this->mostrarPeriodo() === true) {
            $sHtml .= '<td class=contenido>' . $this->getDesplPeriodos()->desplegable() . '</td>';
        }
        $sHtml .= '<td class=contenido>' . $this->getDesplAnys()->desplegable() . '</td>';
        $sHtml .= '<td id="span_fechas" class="d_novisible etiqueta" >   ';
        $sHtml .= _("entre");
        $sHtml .= '	<input type=text id="empiezamin" name="empiezamin" size="12" value="' . $this->sEmpiezaMin . '" class=contenido title="dd/mm/aa"  >';
        $sHtml .= '	' . _("y");
        $sHtml .= '	<input type="text" id="empiezamax" name="empiezamax" size="12" value="' . $this->sEmpiezaMax . '" class=contenido title="dd/mm/aa" >';
        $sHtml .= '</td>';
        if (isset($this->sBoton)) {
            $sHtml .= '<td>' . $this->sBoton . '</td>';
        }
        $sHtml .= '</tr></table>';
        return $sHtml;
    }

    public function mostrarPeriodo(): bool
    {
        $aOpciones = $this->oDesplPeriodos->getOpciones();
        if (is_array($aOpciones) && array_key_exists('ninguno', $aOpciones)) {
            return false;
        }
        return true;
    }

    public function setPosiblesPeriodos($aOpciones): void
    {
        if (!isset($this->oDesplPeriodos)) {
            $this->getDesplPeriodos();
        }
        $this->oDesplPeriodos->setOpciones($aOpciones);
    }

    public function setDesplPeriodosOpcion_sel($sOpcion_sel): void
    {
        if (!isset($this->oDesplPeriodos)) {
            $this->getDesplPeriodos();
        }
        $this->oDesplPeriodos->setOpcion_sel($sOpcion_sel);
    }

    public function getDesplPeriodos(): Desplegable
    {
        if (!isset($this->oDesplPeriodos)) {
            $oDesplPeriodos = new Desplegable();
            $oDesplPeriodos->setNombre('periodo');
            $oDesplPeriodos->setOpciones($this->aOpcionesPeriodos);
            $oDesplPeriodos->setBlanco(true);
            $oDesplPeriodos->setAction('funjs_activar_fecha()');
            $this->oDesplPeriodos = $oDesplPeriodos;
        }
        return $this->oDesplPeriodos;
    }

    public function getOpcionesAnys(): array
    {
        if (empty($this->aOpcionesAnys)) {
            $any = (int)date('Y');
            $aOpcionesAnys[$any - 2] = $any - 2;
            $aOpcionesAnys[$any - 1] = $any - 1;
            $aOpcionesAnys[$any] = $any;
            $aOpcionesAnys[$any + 1] = $any + 1;
            $aOpcionesAnys[$any + 2] = $any + 2;
            $this->aOpcionesAnys = $aOpcionesAnys;
        }
        return $this->aOpcionesAnys;
    }

    public function setPosiblesAnys($aOpciones): void
    {
        if (!isset($this->oDesplAnys)) {
            $this->getDesplAnys();
        }
        $this->oDesplAnys->setOpciones($aOpciones);
    }

    public function getDesplAnys(): Desplegable
    {
        if (!isset($this->oDesplAnys)) {
            $any = (int)date('Y');
            $aOpciones = $this->getOpcionesAnys();
            $oDesplAnys = new Desplegable();
            $oDesplAnys->setNombre('year');
            $oDesplAnys->setOpciones($aOpciones);
            $oDesplAnys->setBlanco(false);
            $oDesplAnys->setOpcion_sel($any);
            if (!empty($this->sAction)) {
                $oDesplAnys->setAction($this->sAction);
            }
            $this->oDesplAnys = $oDesplAnys;
        }
        return $this->oDesplAnys;
    }

    public function setAction($sAction): void
    {
        $this->sAction = $sAction;
    }

    public function setDesplAnys($oDespl): void
    {
        $this->oDesplAnys = $oDespl;
    }

    public function setisDesplAnysVisible(bool $visible): void
    {
        $this->isDesplAnysVisible = $visible;
    }

    public function setDesplAnysOpcion_sel($sOpcion_sel): void
    {
        if (!isset($this->oDesplAnys)) {
            $this->getDesplAnys();
        }
        if (!empty($sOpcion_sel)) {
            $this->oDesplAnys->setOpcion_sel($sOpcion_sel);
        }
    }

    public function setFormName($sFormName): void
    {
        $this->sFormName = $sFormName;
    }

    public function setTitulo($sTitulo): void
    {
        $this->sTitulo = $sTitulo;
    }

    public function setBoton($sBoton): void
    {
        $this->sBoton = $sBoton;
    }

    public function setAntes($sAntes): void
    {
        $this->sAntes = $sAntes;
    }

    public function setEmpiezaMinIso($sEmpiezaMinIso): void
    {
        $oEmpiezamin = new DateTimeLocal($sEmpiezaMinIso);
        $this->sEmpiezaMin = $oEmpiezamin->getFromLocal();
    }

    public function setEmpiezaMaxIso($sEmpiezaMaxIso): void
    {
        $oEmpiezamax = new DateTimeLocal($sEmpiezaMaxIso);
        $this->sEmpiezaMax = $oEmpiezamax->getFromLocal();
    }

    public function setEmpiezaMin($sEmpiezaMin): void
    {
        $this->sEmpiezaMin = $sEmpiezaMin;
    }

    public function setEmpiezaMax($sEmpiezaMax): void
    {
        $this->sEmpiezaMax = $sEmpiezaMax;
    }
}
