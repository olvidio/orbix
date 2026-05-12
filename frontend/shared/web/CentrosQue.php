<?php

namespace frontend\shared\web;

use frontend\shared\PostRequest;

/**
 * Classe que presenta un quadre per buscar diferents centres.
 *
 * No accede a repositorios de `src/`: las opciones del desplegable se
 * obtienen contra el endpoint `/src/ubis/centros_opciones_data` vía
 * {@see PostRequest} (ver `refactor.md` — separación frontend ↔ backend).
 */
class CentrosQue
{
    private ?string $sTitulo = null;
    private array $aCentros = [];
    private ?Desplegable $oDesplCentros = null;
    private mixed $sBoton = null;
    private mixed $sAntes = null;

    /**
     * Filtro whitelisted aplicado al obtener el listado de centros.
     * Claves soportadas: active, sv, sf, id_ubi_in, tipo_ctr.
     * @var array<string, mixed>
     */
    private array $aFiltroCentros = [];

    public function __construct()
    {
    }

    /**
     * Retorna el codi html amb el desplegable de centres.
     */
    public function getHtmlTabla2(): string
    {
        $aOpcionesCentros = $this->getDesplCentros()->getOpciones();
        $oSelects = new DesplegableArray('', $aOpcionesCentros, 'id_ctr');
        $oSelects->setBlanco('t');
        $oSelects->setAccionConjunto('fnjs_mas_centros(event)');
        $sHtml = '<script>
		fnjs_mas_centros = function(evt) {
            let valor=1;
			if(evt!=="x") {
				let id_campo=evt.currentTarget.id;
				valor=$(id_campo).val();
				evt.preventDefault();
				evt.stopPropagation();
			}
			if (evt.keyCode===9 || evt.type==="change" || evt==="x") {
				if (valor!==0) {
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
     * Retorna el codi html amb els radio buttons per escollir un grup de centres sv,sf.
     */
    public function getHtmlTabla(): string
    {
        $aOpcionesCentros = $this->getDesplCentros()->getOpciones();
        $oSelects = new DesplegableArray('', $aOpcionesCentros, 'id_ctr');
        $oSelects->setBlanco('t');
        $oSelects->setAccionConjunto('fnjs_mas_centros(event)');
        $sHtml = '<script>
		fnjs_otro = function(v) {
			if (v===1) {
				$(\'#id_ctr_span\').addClass(\'d_visible\');
				$(\'#ctr_sel_9\').prop("checked",true);
			} else {
				$(\'#id_ctr_span\').html("");	
			}
		}
		fnjs_mas_centros = function(evt) {
			fnjs_otro(1);
			let valor=1;
			if(evt!=="x") {
				let id_campo=evt.currentTarget.id;
				valor=$(id_campo).val();
				evt.preventDefault();
				evt.stopPropagation();
			}
			if (evt.keyCode===9 || evt.type==="change" || evt==="x") {
				if (valor!==0) {
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
        foreach ($this->aCentros as $inum => $sCentro) {
            if ($inum === 9) {
                $sHtml .= '<tr><td><input type="radio" id="ctr_sel_' . $inum . '" name="ctr_sel" value="' . $inum . '" onClick="fnjs_otro(1);">' . $sCentro . '</td>';
                $sHtml .= '<td>' . $oSelects->ListaSelects() . '</td>';
            } else {
                $sHtml .= '<tr><td><input type="radio" id="ctr_sel_' . $inum . '" name="ctr_sel" value="' . $inum . '" onClick="fnjs_otro(0);">' . $sCentro . '</td></tr>';
            }
        }
        $sHtml .= '<tr><td> </td></tr>';
        $sHtml .= '</table>';

        return $sHtml;
    }

    public function setCentros($sQue): void
    {
        $this->aCentros = [];
        switch ($sQue) {
            case 'all':
                $this->aCentros[1] = _("centros sólo sv");
                $this->aCentros[2] = _("centros sólo sf");
                $this->aCentros[3] = _("centros comunes");
                $this->aCentros[4] = _("centros sv");
                $this->aCentros[5] = _("centros sf");
                $this->aCentros[6] = _("centros sv y sf");
                $this->aCentros[9] = _("un centro o lugar");
                $this->aCentros[11] = _("todas las actividades sv");
                $this->aCentros[12] = _("todas las actividades sf");
                break;
            case 'sv':
                $this->aCentros[3] = _("centros comunes");
                $this->aCentros[4] = _("centros sv");
                $this->aCentros[9] = _("un centro o lugar");
                $this->aCentros[11] = _("todas las actividades sv");
                break;
            case 'sf':
                $this->aCentros[3] = _("centros comunes");
                $this->aCentros[5] = _("centros sf");
                $this->aCentros[6] = _("centros sv y sf");
                $this->aCentros[9] = _("un centro o lugar");
                $this->aCentros[12] = _("todas las actividades sf");
                break;
            case 'centro':
                $this->aCentros[9] = _("un centro o lugar");
                break;
        }
    }

    /**
     * Filtro whitelisted para el backend /src/ubis/centros_opciones_data.
     *
     * Claves soportadas:
     *   - 'active'    bool   (default true)
     *   - 'sv'        bool
     *   - 'sf'        bool
     *   - 'id_ubi_in' int[]
     *   - 'tipo_ctr'  string (whitelist: 'seccion_no_s')
     *
     * @param array<string, mixed> $filtro
     */
    public function setFiltroCentros(array $filtro): void
    {
        $this->aFiltroCentros = $filtro;
        if (!isset($this->oDesplCentros)) {
            $oDesplCentros = new Desplegable();
            $oDesplCentros->setNombre('id_ctr');
            $oDesplCentros->setBlanco(true);
            $oDesplCentros->setAction('fnjs_otro(1)');
            $this->oDesplCentros = $oDesplCentros;
        }
        $this->oDesplCentros->setOpciones($this->fetchOpciones($filtro));
    }

    public function getPosiblesCentros(): array
    {
        return $this->oDesplCentros?->getOpciones() ?? [];
    }

    public function getDesplCentros(): Desplegable
    {
        if (!isset($this->oDesplCentros)) {
            $oDesplCentros = new Desplegable();
            $oDesplCentros->setNombre('id_ctr');
            $oDesplCentros->setOpciones($this->fetchOpciones($this->aFiltroCentros));
            $oDesplCentros->setBlanco(true);
            $oDesplCentros->setAction('fnjs_otro(1)');
            $this->oDesplCentros = $oDesplCentros;
        }
        return $this->oDesplCentros;
    }

    public function setAction($sAction): void
    {
        if (!isset($this->oDesplCentros)) {
            $oDesplCentros = new Desplegable();
            $oDesplCentros->setNombre('id_ctr');
            $oDesplCentros->setBlanco(true);
            $this->oDesplCentros = $oDesplCentros;
        }
        $this->oDesplCentros->setAction($sAction);
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

    /**
     * Llama al backend para traer las opciones id_ubi => nombre_ubi.
     *
     * @param array<string, mixed> $filtro
     * @return array<int, string>
     */
    private function fetchOpciones(array $filtro): array
    {
        $campos = self::normalizaFiltroParaPost($filtro);
        $data = PostRequest::getDataFromUrl('/src/ubis/centros_opciones_data', $campos);
        if (!isset($data['opciones']) || !is_array($data['opciones'])) {
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
        if (!empty($filtro['tipo_ctr'])) {
            $campos['tipo_ctr'] = (string)$filtro['tipo_ctr'];
        }
        return $campos;
    }
}
