<?php
namespace web;

/**
 * Classe que presenta un quadre per establir un periode de cerques.
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 26/11/2010
 */
class PeriodoQue
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * sTitulo de PeriodoQue
     *
     * @var string
     */
    private $sTitulo;
    /**
     * oDesplPeriodos de PeriodoQue
     *
     * @var object tipo Desplegble
     */
    private $oDesplPeriodos;
    /**
     * aOpcionesPeriodos de PeriodoQue
     *
     * @var array
     */
    private $aOpcionesPeriodos;
    /* No sé com fer per que funcioni aquí  (el traduir).
               'tot_any' => _("todo el año"),
               'trimestre_1'=>"_("primer trimestre")",
               'trimestre_2'=>"_("segundo trimestre")",
               'trimestre_3'=>"_("tercer trimestre")",
               'trimestre_4'=>"_("cuarto trimestre")",
               'separador'=>'---------',
               'otro'=>"_("otro")"
               );
   */

    /**
     * oDesplAnys de PeriodoQue
     *
     * @var object tipo Desplegble
     */
    private $oDesplAnys;
    /**
     * aOpcionesAnys de PeriodoQue
     *
     * @var array
     */
    private $aOpcionesAnys;
    /**
     * sBoton de PeriodoQue
     *
     * @var string
     */
    private $sBoton;
    /**
     * sAntes de PeriodoQue
     *
     * @var string
     */
    private $sAntes;
    /**
     * sEmpiezaMin de PeriodoQue
     *
     * @var string
     */
    private $sEmpiezaMin;
    /**
     * sEmpiezaMax de PeriodoQue
     *
     * @var string
     */
    private $sEmpiezaMax;
    /**
     * sFormName de PeriodoQue. Nom del formulari al que pertany. Per defecte='modifica'
     *
     * @var string
     */
    private $sFormName;


    /* CONSTRUCTOR ------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     *
     */
    function __construct()
    {
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Retorna una taula on poder triar un periode.
     *
     * @return string
     */
    public function getTd()
    {

        if (empty($this->sFormName)) $this->sFormName = 'modifica';
        $sHtml = '<script>
			funjs_activar_fecha = function() {
				var f=$(\'#periodo\').val();	
				if (f==\'otro\') {
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

        //$sHtml.='<td>';
        //-- para que funcione el script de 'omplir_limits_dates'
        //$sHtml.='<input type=hidden id=iasistentes_val name=iasistentes_val value="x">';
        //$sHtml.='<input type=hidden id=iactividad_val name=iactividad_val value="x">';
        //$sHtml.='</td>';
        if (isset($this->sAntes)) {
            $sHtml .= '<td>' . $this->sAntes . '</td>';
        }
        //-- Final para que funcione
        $sHtml .= '<td class=contenido>' . $this->getDesplPeriodos()->desplegable() . '</td>';
        $sHtml .= '<td class=contenido>' . $this->getDesplAnys()->desplegable() . '</td>';
        //$sHtml.='<td colspan=5 id="span_fechas" class=etiqueta style="visibility: hidden;">   ';
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

    /**
     * Retorna una taula on poder triar un periode.
     *
     * @return string
     */
    public function getHtml()
    {
        $sHtml = '<script>
			funjs_activar_fecha = function() {
				var f=$(\'#periodo\').val();	
				if (f==\'otro\') {
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
        //-- para que funcione el script de 'omplir_limits_dates'
        $sHtml .= '<input type=hidden id=iasistentes_val name=iasistentes_val value="x">';
        $sHtml .= '<input type=hidden id=iactividad_val name=iactividad_val value="x">';
        $sHtml .= '</td>';
        if (isset($this->sAntes)) {
            $sHtml .= '<td>' . $this->sAntes . '</td>';
        }
        //-- Final para que funcione
        if ($this->mostrarPeriodo() === true) {
            $sHtml .= '<td class=contenido>' . $this->getDesplPeriodos()->desplegable() . '</td>';
        }
        $sHtml .= '<td class=contenido>' . $this->getDesplAnys()->desplegable() . '</td>';
        //$sHtml.='<td id="span_fechas" class=etiqueta style="visibility: hidden;">   ';
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

    function mostrarPeriodo()
    {
        $aOpciones = $this->oDesplPeriodos->getOpciones();
        //print_r($aOpciones);
        if (is_array($aOpciones) && array_key_exists('ninguno', $aOpciones)) {
            return false;
        } else {
            return true;
        }
    }

    function setPosiblesPeriodos($aOpciones)
    {
        if (!isset($this->oDesplPeriodos)) {
            $this->getDesplPeriodos();
        }
        $this->oDesplPeriodos->setOpciones($aOpciones);
    }

    function setDesplPeriodosOpcion_sel($sOpcion_sel)
    {
        if (!isset($this->oDesplPeriodos)) {
            $this->getDesplPeriodos();
        }
        $this->oDesplPeriodos->setOpcion_sel($sOpcion_sel);
    }

    function getDesplPeriodos()
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

    function getOpcionesAnys()
    {
        if (empty($this->aOpcionesAnys)) {
            $any = (integer)date('Y');
            $aOpcionesAnys[$any - 2] = $any - 2;
            $aOpcionesAnys[$any - 1] = $any - 1;
            $aOpcionesAnys[$any] = $any;
            $aOpcionesAnys[$any + 1] = $any + 1;
            $aOpcionesAnys[$any + 2] = $any + 2;
            $this->aOpcionesAnys = $aOpcionesAnys;
        }
        return $this->aOpcionesAnys;
    }

    function setPosiblesAnys($aOpciones)
    {
        if (!isset($this->oDesplAnys)) {
            $this->getDesplAnys();
        }
        $this->oDesplAnys->setOpciones($aOpciones);
    }

    function getDesplAnys()
    {
        if (!isset($this->oDesplAnys)) {
            $any = (integer)date('Y');
            $aOpciones = $this->getOpcionesAnys();
            $oDesplAnys = new Desplegable();
            $oDesplAnys->setNombre('year');
            $oDesplAnys->setOpciones($aOpciones);
            $oDesplAnys->setBlanco(false);
            $oDesplAnys->setOpcion_sel($any);
            $this->oDesplAnys = $oDesplAnys;
        }
        return $this->oDesplAnys;
    }

    function setDesplAnys($oDespl)
    {
        $this->oDesplAnys = $oDespl;
    }

    function setDesplAnysOpcion_sel($sOpcion_sel)
    {
        if (!isset($this->oDesplAnys)) {
            $this->getDesplAnys();
        }
        // si está en blanco pongo la opción por defecto
        if (!empty($sOpcion_sel)) {
            $this->oDesplAnys->setOpcion_sel($sOpcion_sel);
        }
    }

    function setFormName($sFormName)
    {
        $this->sFormName = $sFormName;
    }

    function setTitulo($sTitulo)
    {
        $this->sTitulo = $sTitulo;
    }

    function setBoton($sBoton)
    {
        $this->sBoton = $sBoton;
    }

    function setAntes($sAntes)
    {
        $this->sAntes = $sAntes;
    }

    function setEmpiezaMinIso($sEmpiezaMinIso)
    {
        $oEmpiezamin = new DateTimeLocal($sEmpiezaMinIso);
        $sEmpiezaMin = $oEmpiezamin->getLocal();
        $this->sEmpiezaMin = $sEmpiezaMin;
    }

    function setEmpiezaMaxIso($sEmpiezaMaxIso)
    {
        $oEmpiezamax = new DateTimeLocal($sEmpiezaMaxIso);
        $sEmpiezaMax = $oEmpiezamax->getLocal();
        $this->sEmpiezaMax = $sEmpiezaMax;
    }

    function setEmpiezaMin($sEmpiezaMin)
    {
        $this->sEmpiezaMin = $sEmpiezaMin;
    }

    function setEmpiezaMax($sEmpiezaMax)
    {
        $this->sEmpiezaMax = $sEmpiezaMax;
    }
}

?>
