<?php

namespace web;

use ubis\model\entity as ubis;

/**
 * Classe que presenta un quadre per buscar diferents cases.
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 26/11/2010
 */
class CasasQue
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * sTitulo de CasaQue
     *
     * @var string
     */
    private $sTitulo;
    /**
     * aCasas de CasaQue
     *
     * @var array
     */
    private $aCasas;
    /**
     * oDesplCasas de CasaQue
     *
     * @var object tipo Desplegble
     */
    private $oDesplCasas;

    /**
     *
     * @var array
     */
    private $sSeleccionados;

    /* CONSTRUCTOR -------------------------------------------------------------- */
    private int $cdc_sel;

    /**
     * Constructor de la classe.
     *
     */
    function __construct()
    {
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Retorna el codi html amb el desplegable de cases.
     *
     * @return string
     */
    public function getHtmlTabla2()
    {
        $aOpcionesCasas = $this->getDesplCasas()->getOpciones();
        $oSelects = new DesplegableArray('', $aOpcionesCasas, 'id_cdc');
        $oSelects->setBlanco('t');
        $oSelects->setAccionConjunto('fnjs_mas_casas(event)');
        if (!empty($this->sSeleccionados)) {
            $oSelects->setSeleccionados($this->sSeleccionados);
        }

        $sHtml = '<script>
		fnjs_mas_casas = function(evt) {
			if(evt=="x") {
				var valor=1;
			} else {
				var id_campo=evt.currentTarget.id;
				var valor=$(id_campo).val();
				evt.preventDefault();
				evt.stopPropagation();
			}
			if (evt.keyCode==9 || evt.type=="change" || evt=="x") {
				if (valor!=0) {
					' . $oSelects->ListaSelectsJs() . '
				}
			}
		}';
        $sHtml .= $oSelects->ComprobarSelectJs();
        $sHtml .= '</script>';
        $sHtml .= '<table>';
        if (!empty($this->sTitulo)) {
            $sHtml .= '<tr><th class=titulo_inv colspan="6">';
            $sHtml .= $this->sTitulo;
            $sHtml .= '</th></tr>';
        }
        $sHtml .= '<tr>';
        if (isset($this->sAntes)) {
            $sHtml .= '<td>' . $this->sAntes . '</td>';
        }
        $sHtml .= '<td>' . $oSelects->ListaSelects() . '</td>';
        if (isset($this->sBoton)) {
            $sHtml .= '<td>' . $this->sBoton . '</td>';
        }
        $sHtml .= '</tr></table>';
        return $sHtml;
    }

    /**
     * Retorna el codi html amb els radio buttons per escollir un grup de cases sv,sf.
     *
     * @return string
     */
    public function getHtmlTabla()
    {
        $aOpcionesCasas = $this->getDesplCasas()->getOpciones();
        $oSelects = new DesplegableArray('', $aOpcionesCasas, 'id_cdc');
        $oSelects->setBlanco('t');
        $oSelects->setAccionConjunto('fnjs_mas_casas(event)');
        if (!empty($this->sSeleccionados)) {
            $oSelects->setSeleccionados($this->sSeleccionados);
        }

        $sHtml = '<script>
		funjs_otro = function(v) {
			if (v==1) {
				$(\'#id_cdc_span\').addClass(\'d_visible\');
				$(\'#cdc_sel_9\').prop("checked",true);
			} else {
				$(\'#id_cdc_span\').html("");	
			}
		}
		fnjs_mas_casas = function(evt) {
			funjs_otro(1);
			if(evt=="x") {
				var valor=1;
			} else {
				var id_campo=evt.currentTarget.id;
				var valor=$(id_campo).val();
				evt.preventDefault();
				evt.stopPropagation();
			}
			if (evt.keyCode==9 || evt.type=="change" || evt=="x") {
				if (valor!=0) {
					' . $oSelects->ListaSelectsJs() . '
				}
			}
		}';
        $sHtml .= $oSelects->ComprobarSelectJs();
        $sHtml .= '</script>';
        $sHtml .= '<table>';
        $sHtml .= '<tr><th class=titulo_inv colspan="3">';
        $sHtml .= $this->sTitulo;
        $sHtml .= '</th></tr>';
        foreach ($this->aCasas as $inum => $sCasa) {
            $chk_cdc = '';
            if ($inum === $this->getCdcSel()) {
                $chk_cdc = 'checked';
            }
            if ($inum === 9) {
                $sHtml .= '<tr><td><input type="radio" id="cdc_sel_' . $inum . '" name="cdc_sel" value="' . $inum . '" onClick="funjs_otro(1);" '.$chk_cdc.'>' . $sCasa . '</td>';
                // para seleccionar más de una casa
                $sHtml .= '<td>' . $oSelects->ListaSelects() . '</td>';
            } else {
                $sHtml .= '<tr><td><input type="radio" id="cdc_sel_' . $inum . '" name="cdc_sel" value="' . $inum . '" onClick="funjs_otro(0);" '.$chk_cdc.'>' . $sCasa . '</td></tr>';
            }
        }
        $sHtml .= '<tr><td> </td></tr>';
        $sHtml .= '</table>';

        return $sHtml;
    }

    function setCasas($sQue)
    {
        $this->aCasas = array();
        switch ($sQue) {
            case 'all':
                $this->aCasas[1] = _("casas sólo sv");
                $this->aCasas[2] = _("casas sólo sf");
                $this->aCasas[3] = _("casas comunes");
                $this->aCasas[4] = _("casas sv");
                $this->aCasas[5] = _("casas sf");
                $this->aCasas[6] = _("casas y ctr sf");
                $this->aCasas[9] = _("una casa o lugar");
                $this->aCasas[11] = _("todas las actividades sv");
                $this->aCasas[12] = _("todas las actividades sf");
                break;
            case 'sv':
                $this->aCasas[3] = _("casas comunes");
                $this->aCasas[4] = _("casas sv");
                $this->aCasas[9] = _("una casa o lugar");
                $this->aCasas[11] = _("todas las actividades sv");
                break;
            case 'sf':
                $this->aCasas[3] = _("casas comunes");
                $this->aCasas[5] = _("casas sf");
                $this->aCasas[6] = _("casas y ctr sf");
                $this->aCasas[9] = _("una casa o lugar");
                $this->aCasas[12] = _("todas las actividades sf");
                break;
            case 'casa':
                $this->aCasas[9] = _("una casa o lugar");
                break;
        }
    }

    function setPosiblesCasas($sCondicion)
    {
        if (!isset($this->oDesplCasas)) {
            $oDesplCasas = new Desplegable();
            $oDesplCasas->setNombre('id_cdc');
            $oDesplCasas->setBlanco(true);
            $oDesplCasas->setAction('funjs_otro(1)');
            $this->oDesplCasas = $oDesplCasas;
        }
        $oGesCasas = new ubis\GestorCasaDl();
        $oOpciones = $oGesCasas->getPosiblesCasas($sCondicion);
        $oDesplCasas->setOpciones($oOpciones);
        $this->oDesplCasas = $oDesplCasas;
    }

    function getPosiblesCasas()
    {
        return $this->oDesplCasas->getOpciones();
    }

    function getDesplCasas()
    {
        if (!isset($this->oDesplCasas)) {
            $oGesCasas = new ubis\GestorCasaDl();
            $oOpciones = $oGesCasas->getPosiblesCasas();
            $oDesplCasas = new Desplegable();
            $oDesplCasas->setNombre('id_cdc');
            $oDesplCasas->setOpciones($oOpciones);
            $oDesplCasas->setBlanco(true);
            $oDesplCasas->setAction('funjs_otro(1)');
            $this->oDesplCasas = $oDesplCasas;
        }
        return $this->oDesplCasas;
    }

    function setAction($sAction)
    {
        if (!isset($this->oDesplCasas)) {
            $oDesplCasas = new Desplegable();
            $oDesplCasas->setNombre('id_cdc');
            $oDesplCasas->setBlanco(true);
            $this->oDesplCasas = $oDesplCasas;
        }
        $this->oDesplCasas->setAction($sAction);
    }

    public function setCasasSel($sCasas)
    {

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

    public function setSeleccionados($sSeleccionados)
    {
        $this->sSeleccionados = $sSeleccionados;
    }

    public function setCdcSel(int $cdc_sel): void
    {
        $this->cdc_sel = $cdc_sel;
    }

    public function getCdcSel(): int
    {
        if (isset($this->cdc_sel)) {
            return $this->cdc_sel;
        }
        return 0;
    }
}
