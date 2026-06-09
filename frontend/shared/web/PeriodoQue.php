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
    private ?Desplegable $oDesplAnys = null;
    private bool $isDesplAnysVisible = true;
    /** @var array<int, string>|null */
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
        $aOpciones = $this->getDesplPeriodos()->getOpciones();
        if (is_array($aOpciones) && array_key_exists('ninguno', $aOpciones)) {
            return false;
        }
        return true;
    }

    /**
     * @param array<int|string, string> $aOpciones
     */
    public function setPosiblesPeriodos(array $aOpciones): void
    {
        $this->getDesplPeriodos()->setOpciones($aOpciones);
    }

    public function setDesplPeriodosOpcion_sel(string $sOpcion_sel): void
    {
        $this->getDesplPeriodos()->setOpcion_sel($sOpcion_sel);
    }

    public function getDesplPeriodos(): Desplegable
    {
        if (!isset($this->oDesplPeriodos)) {
            $oDesplPeriodos = new Desplegable();
            $oDesplPeriodos->setNombre('periodo');
            $oDesplPeriodos->setOpciones([]);
            $oDesplPeriodos->setBlanco(true);
            $oDesplPeriodos->setAction('funjs_activar_fecha()');
            $this->oDesplPeriodos = $oDesplPeriodos;
        }
        return $this->oDesplPeriodos;
    }

    /**
     * @return array<int, string>
     */
    public function getOpcionesAnys(): array
    {
        if (empty($this->aOpcionesAnys)) {
            $any = (int)date('Y');
            $aOpcionesAnys = [];
            foreach ([$any - 2, $any - 1, $any, $any + 1, $any + 2] as $year) {
                $aOpcionesAnys[$year] = (string) $year;
            }
            $this->aOpcionesAnys = $aOpcionesAnys;
        }
        return $this->aOpcionesAnys;
    }

    /**
     * @param array<int|string, string> $aOpciones
     */
    public function setPosiblesAnys(array $aOpciones): void
    {
        $this->getDesplAnys()->setOpciones($aOpciones);
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
            $oDesplAnys->setOpcion_sel((string) $any);
            if (!empty($this->sAction)) {
                $oDesplAnys->setAction($this->sAction);
            }
            $this->oDesplAnys = $oDesplAnys;
        }
        return $this->oDesplAnys;
    }

    public function setAction(string $sAction): void
    {
        $this->sAction = $sAction;
    }

    public function setDesplAnys(Desplegable $oDespl): void
    {
        $this->oDesplAnys = $oDespl;
    }

    public function setisDesplAnysVisible(bool $visible): void
    {
        $this->isDesplAnysVisible = $visible;
    }

    public function setDesplAnysOpcion_sel(string $sOpcion_sel): void
    {
        if ($sOpcion_sel !== '') {
            $this->getDesplAnys()->setOpcion_sel($sOpcion_sel);
        }
    }

    public function setFormName(string $sFormName): void
    {
        $this->sFormName = $sFormName;
    }

    public function setTitulo(string $sTitulo): void
    {
        $this->sTitulo = $sTitulo;
    }

    public function setBoton(string $sBoton): void
    {
        $this->sBoton = $sBoton;
    }

    public function setAntes(string $sAntes): void
    {
        $this->sAntes = $sAntes;
    }

    public function setEmpiezaMinIso(string $sEmpiezaMinIso): void
    {
        $oEmpiezamin = new DateTimeLocal($sEmpiezaMinIso);
        $this->sEmpiezaMin = $oEmpiezamin->getFromLocal();
    }

    public function setEmpiezaMaxIso(string $sEmpiezaMaxIso): void
    {
        $oEmpiezamax = new DateTimeLocal($sEmpiezaMaxIso);
        $this->sEmpiezaMax = $oEmpiezamax->getFromLocal();
    }

    public function setEmpiezaMin(string $sEmpiezaMin): void
    {
        $this->sEmpiezaMin = $sEmpiezaMin;
    }

    public function setEmpiezaMax(string $sEmpiezaMax): void
    {
        $this->sEmpiezaMax = $sEmpiezaMax;
    }
}
