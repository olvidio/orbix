<?php

namespace frontend\shared\web;

use frontend\shared\PostRequest;

/**
 * Classe que presenta un quadre per buscar diferents cases.
 *
 * No accede a repositorios de `src/`: las opciones del desplegable se
 * obtienen contra el endpoint `/src/ubis/casas_opciones_data` vía
 * {@see PostRequest} (ver `refactor.md` — separación frontend ↔ backend).
 */
class CasasQue
{
    private ?string $sTitulo = null;
    private array $aCasas = [];
    private ?Desplegable $oDesplCasas = null;
    private ?string $sSeleccionados = null;
    private int $cdc_sel;
    private mixed $sBoton = null;
    private mixed $sAntes = null;

    /**
     * Filtro whitelisted aplicado al obtener el listado de casas.
     * Claves soportadas: active, sv, sf, id_ubi_in.
     * @var array<string, mixed>
     */
    private array $aFiltroCasas = [];

    public function __construct()
    {
    }

    /**
     * Retorna el codi html amb el desplegable de cases.
     */
    public function getHtmlTabla2(): string
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
     */
    public function getHtmlTabla(): string
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
                $sHtml .= '<tr><td><input type="radio" id="cdc_sel_' . $inum . '" name="cdc_sel" value="' . $inum . '" onClick="funjs_otro(1);" ' . $chk_cdc . '>' . $sCasa . '</td>';
                $sHtml .= '<td>' . $oSelects->ListaSelects() . '</td>';
            } else {
                $sHtml .= '<tr><td><input type="radio" id="cdc_sel_' . $inum . '" name="cdc_sel" value="' . $inum . '" onClick="funjs_otro(0);" ' . $chk_cdc . '>' . $sCasa . '</td></tr>';
            }
        }
        $sHtml .= '<tr><td> </td></tr>';
        $sHtml .= '</table>';

        return $sHtml;
    }

    public function setCasas($sQue): void
    {
        $this->aCasas = [];
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

    /**
     * Filtro whitelisted para el backend /src/ubis/casas_opciones_data.
     *
     * Claves soportadas:
     *   - 'active'    bool  (default true)
     *   - 'sv'        bool
     *   - 'sf'        bool
     *   - 'id_ubi_in' int[]
     *
     * @param array<string, mixed> $filtro
     */
    public function setFiltroCasas(array $filtro): void
    {
        $this->aFiltroCasas = $filtro;
        if (!isset($this->oDesplCasas)) {
            $oDesplCasas = new Desplegable();
            $oDesplCasas->setNombre('id_cdc');
            $oDesplCasas->setBlanco(true);
            $oDesplCasas->setAction('funjs_otro(1)');
            $this->oDesplCasas = $oDesplCasas;
        }
        $this->oDesplCasas->setOpciones($this->fetchOpciones($filtro));
    }

    public function getPosiblesCasas(): array
    {
        return $this->oDesplCasas?->getOpciones() ?? [];
    }

    public function getDesplCasas(): Desplegable
    {
        if (!isset($this->oDesplCasas)) {
            $oDesplCasas = new Desplegable();
            $oDesplCasas->setNombre('id_cdc');
            $oDesplCasas->setOpciones($this->fetchOpciones($this->aFiltroCasas));
            $oDesplCasas->setBlanco(true);
            $oDesplCasas->setAction('funjs_otro(1)');
            $this->oDesplCasas = $oDesplCasas;
        }
        return $this->oDesplCasas;
    }

    public function setAction($sAction): void
    {
        if (!isset($this->oDesplCasas)) {
            $oDesplCasas = new Desplegable();
            $oDesplCasas->setNombre('id_cdc');
            $oDesplCasas->setBlanco(true);
            $this->oDesplCasas = $oDesplCasas;
        }
        $this->oDesplCasas->setAction($sAction);
    }

    public function setCasasSel($sCasas): void
    {
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

    public function setSeleccionados($sSeleccionados): void
    {
        $this->sSeleccionados = $sSeleccionados;
    }

    public function setCdcSel(int $cdc_sel): void
    {
        $this->cdc_sel = $cdc_sel;
    }

    public function getCdcSel(): int
    {
        return $this->cdc_sel ?? 0;
    }

    /**
     * Llama al backend para traer las opciones id_ubi => nombre_ubi.
     *
     * @param array<string, mixed> $filtro
     * @return array<int, string>
     */
    private function fetchOpciones(array $filtro): array
    {
        $campos = self::normalizaFiltroParaPost($filtro);
        $data = PostRequest::getDataFromUrl('/src/ubis/casas_opciones_data', $campos);
        if (!is_array($data) || !isset($data['opciones']) || !is_array($data['opciones'])) {
            return [];
        }
        return $data['opciones'];
    }

    /**
     * Adapta el filtro (bool/arrays) al formato aceptado por Hash/form_params.
     *
     * @param array<string, mixed> $filtro
     * @return array<string, mixed>
     */
    private static function normalizaFiltroParaPost(array $filtro): array
    {
        $campos = [];
        foreach (['active', 'sv', 'sf'] as $k) {
            if (array_key_exists($k, $filtro)) {
                $campos[$k] = $filtro[$k] ? '1' : '0';
            }
        }
        if (!empty($filtro['id_ubi_in']) && is_array($filtro['id_ubi_in'])) {
            $ids = array_values(array_filter(array_map('intval', $filtro['id_ubi_in']), static fn ($v) => $v > 0));
            if (!empty($ids)) {
                $campos['id_ubi_in'] = implode(',', $ids);
            }
        }
        return $campos;
    }
}
