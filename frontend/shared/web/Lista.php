<?php

namespace frontend\shared\web;

use frontend\shared\PostRequest;
use function frontend\shared\helpers\is_true;

/**
 * Classe per gestionar llistes de dades tipus taula (html o SlickGrid).
 */
class Lista
{
    /** @var list<string>|array<int|string, string> */
    private array $aGrupos = [];
    private bool $botones_grupo = false;
    /** @var list<array<string, mixed>|string> */
    private array $aCabeceras = [];
    private string $sPie = '';
    private string $ssortCol = '';
    /** @var array<string, bool|string> */
    private array $aColVisible = [];
    /** @var array<int|string, mixed> */
    private array $aDatos = [];
    /** @var list<array<string, mixed>> */
    private array $aBotones = [];
    private string $sid_tabla = 'uno';
    private bool $bFiltro = true;
    private bool $bColVis = true;
    private string $formato_tabla = '';
    private bool $bMultiSort = false;
    /** Checkbox por fila sin botones de acción en la cabecera (p. ej. zona_ctr). */
    private bool $bConSel = false;

    public function __construct()
    {
    }

    private static function scalarString(mixed $value): string
    {
        if ($value === null) {
            return '';
        }
        if (is_string($value)) {
            return $value;
        }
        if (is_scalar($value)) {
            return (string) $value;
        }

        return '';
    }

    private static function slickgridDimension(mixed $value, ?string $fallback = null): ?string
    {
        $s = self::scalarString($value);
        if ($s === '' || !is_numeric($s)) {
            return $fallback;
        }

        return $s;
    }

    private static function jsQuotedString(string $value): string
    {
        return '"' . addslashes($value) . '"';
    }

    /**
     * @return list<string>
     */
    private static function normalizeSelectedIds(mixed $selectRaw): array
    {
        if (is_array($selectRaw)) {
            $out = [];
            foreach ($selectRaw as $item) {
                $s = self::scalarString($item);
                if ($s !== '') {
                    $out[] = $s;
                }
            }

            return $out;
        }
        if ($selectRaw === null || $selectRaw === '') {
            return [];
        }

        return [self::scalarString($selectRaw)];
    }

    /** @param array<string, mixed>|string $cabecera */
    private static function cabeceraFieldKey(array|string $cabecera, string $key): ?string
    {
        if (!is_array($cabecera)) {
            return null;
        }
        $val = $cabecera[$key] ?? null;
        if ($val === null || $val === '') {
            return null;
        }

        return self::scalarString($val);
    }

    /**
     * @param array<string, mixed>|string $cabecera
     * @return array{class: string, width: string, visible: bool, name: string}
     */
    private static function cabeceraHtmlMeta(array|string $cabecera): array
    {
        $class = '';
        $width = '';
        $visible = true;
        $name = self::cabeceraFieldKey($cabecera, 'name') ?? self::scalarString($cabecera);
        if (is_array($cabecera)) {
            if (!empty($cabecera['class'])) {
                $class = 'class="' . self::scalarString($cabecera['class']) . '"';
            }
            if (!empty($cabecera['width'])) {
                $width = 'width="' . self::scalarString($cabecera['width']) . '"';
            }
            $vis = $cabecera['visible'] ?? null;
            if ($vis !== null && $vis !== '' && strtolower(self::scalarString($vis)) === 'no') {
                $visible = false;
            }
        }

        return ['class' => $class, 'width' => $width, 'visible' => $visible, 'name' => $name];
    }

    /** @param array<string, mixed> $valor */
    private static function renderArrayValorTd(array $valor): string
    {
        $val = self::scalarString($valor['valor'] ?? '');
        $html = '<td>';
        if (!empty($valor['ira'])) {
            $ira = self::scalarString($valor['ira']);
            $html .= '<span class="link" onclick="fnjs_update_div(\'#main\',\'' . $ira . '\')" >' . $val . '</span>';
        } elseif (!empty($valor['script'])) {
            $ira = self::scalarString($valor['script']);
            $html .= '<span class="link" onclick=\'' . $ira . '\' >' . $val . '</span>';
        } else {
            $html .= $val;
        }
        for ($idx = 2; $idx <= 3; $idx++) {
            if (!empty($valor["ira$idx"])) {
                $ira = self::scalarString($valor["ira$idx"]);
                $html .= ' <span class="link" onclick="fnjs_update_div(\'#main\',\'' . $ira . '\')" >' . $val . '</span>';
            }
            if (!empty($valor["script$idx"])) {
                $ira = self::scalarString($valor["script$idx"]);
                $html .= ' <span class="link" onclick=\'' . $ira . '\' >' . $val . '</span>';
            }
        }

        return $html . '</td>';
    }

    private static function renderScalarValorTd(mixed $valor): string
    {
        $text = self::scalarString($valor);
        $fechaIso = self::formatFechaIsoFromDmy($text);
        if ($fechaIso !== null) {
            return "<td class='fecha' fecha_iso='$fechaIso'>$text</td>";
        }

        return '<td>' . $text . '</td>';
    }

    private static function formatFechaIsoFromDmy(string $valor): ?string
    {
        if (!preg_match("/^(\d)+[\/-](\d)+[\/-](\d\d)+$/", $valor)) {
            return null;
        }
        $parts = preg_split('/[:\/\.-]/', $valor);
        if (!is_array($parts) || count($parts) < 3) {
            return null;
        }
        $ts = mktime(0, 0, 0, (int) $parts[1], (int) $parts[0], (int) $parts[2]);
        if ($ts === false) {
            return null;
        }

        return date('Y-m-d', $ts);
    }

    /** @param array<int|string, mixed> $fila */
    private static function resolveStartIcol(array $fila): int
    {
        if (!isset($fila[0]) && isset($fila[1])) {
            return 1;
        }
        if (!isset($fila[0])) {
            return 0;
        }
        $isId = false;
        if (isset($fila['sel'])) {
            $sel = $fila['sel'];
            $selId = is_array($sel) ? self::scalarString($sel['id'] ?? '') : self::scalarString($sel);
            $fila0 = self::scalarString($fila[0]);
            if (strpos($selId, $fila0) !== false || preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}/i', $fila0)) {
                $isId = true;
            }
        } elseif (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}/i', self::scalarString($fila[0]))) {
            $isId = true;
        }

        return $isId ? 1 : 0;
    }

    /**
     * @param array<int|string, mixed> $fila
     * @param list<array<string, mixed>|string> $aCabeceras
     * @param array<int, bool> $aColsVisible
     */
    private static function renderCeldasDatos(array $fila, array $aCabeceras, array $aColsVisible, int $startIcol): string
    {
        $tbody = '';
        $icolOffset = 0;
        foreach ($aCabeceras as $numCol => $cabecera) {
            if (empty($aColsVisible[$numCol])) {
                continue;
            }
            $field = self::cabeceraFieldKey($cabecera, 'field');
            $id = self::cabeceraFieldKey($cabecera, 'id');
            $valor = '';
            if ($field !== null && array_key_exists($field, $fila)) {
                $valor = $fila[$field];
            } elseif ($id !== null && array_key_exists($id, $fila)) {
                $valor = $fila[$id];
            } else {
                $targetIdx = $startIcol + $icolOffset;
                $valor = $fila[$targetIdx] ?? '';
                $icolOffset++;
            }
            if (is_array($valor) && array_key_exists('valor', $valor)) {
                $tbody .= self::renderArrayValorTd($valor);
            } else {
                $tbody .= self::renderScalarValorTd($valor);
            }
        }

        return $tbody;
    }

    private function resolveIdiomaCode(): string
    {
        $idioma = '';
        if (isset($_SESSION['session_auth']) && is_array($_SESSION['session_auth'])) {
            $authIdioma = $_SESSION['session_auth']['idioma'] ?? null;
            if (is_string($authIdioma)) {
                $idioma = $authIdioma;
            }
        }
        if ($idioma === '' && isset($_SESSION['oConfig']) && is_object($_SESSION['oConfig']) && method_exists($_SESSION['oConfig'], 'getIdioma_default')) {
            $idioma = self::scalarString($_SESSION['oConfig']->getIdioma_default());
        }
        $aIdioma = explode('.', $idioma);

        return $aIdioma[0];
    }

    /**
     * Llama al endpoint backend que devuelve las preferencias de tabla para
     * el usuario actual. Se usa para decidir HTML vs SlickGrid y, para
     * SlickGrid, recuperar colVisible/colWidths/tamaños.
     *
     * @return array{formato_tabla: string, slickgrid: array<int|string, mixed>|null}
     */
    private function fetchPreferenciaTabla(string $id_tabla = ''): array
    {
        $data = PostRequest::getDataFromUrl(
            '/src/usuarios/preferencia_tabla_get',
            ['id_tabla' => $id_tabla]
        );
        $formato = $data['formato_tabla'] ?? '';
        $slickgrid = $data['slickgrid'] ?? null;

        return [
            'formato_tabla' => self::scalarString($formato),
            'slickgrid' => is_array($slickgrid) ? $slickgrid : null,
        ];
    }

    /**
     * Muestra una tabla simple.
     */
    public function lista(): string
    {
        $aCabeceras = $this->aCabeceras;
        $aDatos = $this->aDatos;
        $clase = 'lista';
        $cabecera = "";
        $cab = 1;
        $aColsVisible = [];
        $num_col = 0;
        foreach ($aCabeceras as $Cabecera) {
            $meta = self::cabeceraHtmlMeta($Cabecera);
            $aColsVisible[$num_col] = $meta['visible'];
            $num_col++;

            if ($meta['visible']) {
                if ($meta['name'] !== '') {
                    $cabecera .= '<th class=cabecera ' . $meta['width'] . ' ' . $meta['class'] . ' >' . trim($meta['name']) . "</th>\n";
                } else {
                    $cabecera .= '<th class=cabecera tipo=\'notext\' ' . $meta['width'] . ' ' . $meta['class'] . " ></th>\n";
                }
                $cab++;
            }
        }

        $Html = "<table class=\"$clase\"><tr>";
        $Html .= $cabecera . "</tr>";

        if (empty($aDatos)) {
            return _("no hay ninguna fila");
        }
        $tbody = "<tbody class=\"$clase\">";

        $f = 1;
        foreach ($aDatos as $num_fila => $fila) {
            if (!is_array($fila)) {
                continue;
            }
            $clase = "imp";
            $f % 2 ? 0 : $clase = "par";
            $f++;
            if (!empty($fila['clase'])) {
                $clase .= ' ' . self::scalarString($fila['clase']);
            }
            $tbody .= "<tr class='$clase' >";
            $tbody .= self::renderCeldasDatos($fila, $aCabeceras, $aColsVisible, self::resolveStartIcol($fila));
            $tbody .= "</tr>\n";
        }

        $Html .= $tbody . "</tbody></table>";
        return $Html;
    }

    /**
     * Devuelve una lista paginada agrupada por los índices de `aGrupos`.
     */
    public function listaPaginada(): string
    {
        $aGrupos = $this->aGrupos;
        $aDatos = $this->aDatos;
        reset($aGrupos);
        $Html = '';
        foreach ($aGrupos as $key => $titulo) {
            $grupoDatos = $aDatos[$key] ?? [];
            $this->aDatos = is_array($grupoDatos) ? $grupoDatos : [];
            $Html .= "<div class=salta_pag>";
            $Html .= "<h2>$titulo</h2>";
            $Html .= $this->lista();
            if (!empty($this->sPie)) {
                $Html .= "<p>$this->sPie</p>";
            }
            $Html .= "</div>";
        }
        return $Html;
    }

    public function mostrar_tabla_grupos(): string
    {
        $aGrupos = $this->aGrupos;
        $a_botones = $this->aBotones;
        $aDatos = $this->aDatos;
        $id_tabla = $this->sid_tabla;
        reset($aGrupos);
        $Html = '';
        if ($a_botones !== []) {
            $botones = '';
            $b = 0;
            foreach ($a_botones as $a_boton) {
                $prefix = empty($a_boton['prefix']) ? '' : self::scalarString($a_boton['prefix']) . '_';
                $btn = $prefix . 'btn' . $b++;
                $botones .= '<INPUT id="' . $btn . '" name="' . $btn . '" type=button value="' . self::scalarString($a_boton['txt'] ?? '') . '" onClick=\'' . self::scalarString($a_boton['click'] ?? '') . '\'>';
            }
            $botones .= '</td></tr>';
            $cab = count($this->aCabeceras);
            $botones = '<tr class=botones><td colspan=\'' . $cab . '\'>' . $botones;
            $this->botones_grupo = true;
            $Html .= '<table>' . $botones . "</table>\n";
        }
        $this->setBotones([]);
        foreach ($aGrupos as $key => $titulo) {
            $grupoDatos = $aDatos[$key] ?? [];
            $this->aDatos = is_array($grupoDatos) ? $grupoDatos : [];
            $id_tabla_key = $id_tabla . '_' . $key;
            $this->setId_tabla($id_tabla_key);
            $Html .= "<div class=salta_pag>";
            $Html .= "<h3>$titulo</h3>";
            $Html .= $this->mostrar_tabla_html();
            if (!empty($this->sPie)) {
                $Html .= "<p>$this->sPie</p>";
            }
            $Html .= "</div>";
        }
        return $Html;
    }

    /**
     * Muestra una tabla ordenable, con botones en la cabecera y check box en cada línea.
     * Según la preferencia de usuario (tipo `tabla_presentacion`) delega en SlickGrid o en HTML.
     */
    public function mostrar_tabla(): string
    {
        if (empty($this->formato_tabla)) {
            $prefs = $this->fetchPreferenciaTabla();
            $sPrefs = $prefs['formato_tabla'];
        } else {
            $sPrefs = $this->formato_tabla;
        }
        if ($sPrefs === 'html') {
            return $this->mostrar_tabla_html();
        }

        return $this->mostrar_tabla_slickgrid();
    }

    /**
     * Muestra una tabla SlickGrid ordenable, con botones en la cabecera y checkbox en cada línea.
     *
     * Ver documentación en el original `apps/web/Lista.php` (o en README) sobre el formato
     * admitido para `aCabeceras` y `aDatos` (soporta links `ira`, scripts, `span`, etc.).
     */
    public function mostrar_tabla_slickgrid(): string
    {
        $a_botones = $this->aBotones;
        $a_cabeceras = $this->aCabeceras;
        $a_valores = $this->aDatos;
        $id_tabla = $this->sid_tabla;
        $grid_width = '900';
        $grid_height = '0';

        $sortcol = $this->ssortCol;
        $botones = "";
        $tt = "";
        $numBotones = 0;
        $scroll_id = !empty($a_valores['scroll_id']) ? self::scalarString($a_valores['scroll_id']) : '0';
        unset($a_valores['scroll_id']);
        $a_valores_chk = self::normalizeSelectedIds($a_valores['select'] ?? null);
        unset($a_valores['select']);
        if ($a_valores === []) {
            return _("no hay ninguna fila");
        }
        foreach ($a_botones as $a_boton) {
            $prefix = empty($a_boton['prefix']) ? '' : self::scalarString($a_boton['prefix']) . '_';
            $btn = $prefix . 'btn' . $numBotones++;
            $botones .= '<INPUT id="' . $btn . '" name="' . $btn . '" type=button value="' . self::scalarString($a_boton['txt'] ?? '') . '" onClick=\'' . self::scalarString($a_boton['click'] ?? '') . '\'>';
        }

        /** @var array<string, mixed>|null $aColsVisible */
        $aColsVisible = null;
        /** @var array<string, mixed> $aColsWidth */
        $aColsWidth = [];
        $bPanelVis = false;
        $aPrefs = $this->fetchPreferenciaTabla($id_tabla)['slickgrid'] ?? null;
        if (is_array($aPrefs)) {
            if (!empty($aPrefs['colVisible']) && is_array($aPrefs['colVisible'])) {
                $aColsVisible = $aPrefs['colVisible'];
            }
            $bPanelVis = self::scalarString($aPrefs['panelVis'] ?? '') === 'si';
            if (!empty($aPrefs['colWidths']) && is_array($aPrefs['colWidths'])) {
                $aColsWidth = $aPrefs['colWidths'];
            }
            $grid_width = self::slickgridDimension($aPrefs['widthGrid'] ?? null, '900') ?? '900';
            $grid_height = self::slickgridDimension($aPrefs['heightGrid'] ?? null, '0') ?? '0';
        }
        if ($this->aColVisible !== []) {
            $aColsVisible = $this->aColVisible;
        }
        if (!$this->bFiltro) {
            $bPanelVis = false;
        }

        $c = 0;
        $cf = 0;
        $cv = 0;
        $sColumns = '[';
        $sColumnsVisible = '[';
        $sColFilters = '[';
        $aFields = [];
        $showSelCol = ($numBotones > 0) || $this->bConSel;
        if ($showSelCol) {
            $c++;
            $width = self::slickgridDimension($aColsWidth['sel'] ?? null, '30') ?? '30';
            $selCol = '{id: "sel", name: "sel", field: "sel", width:' . $width . ', sortable: false, formatter: checkboxSelectionFormatter}';
            $sColumns .= $selCol;
            if ($aColsVisible === null || !array_key_exists('sel', $aColsVisible) || is_true($aColsVisible['sel'])) {
                $sColumnsVisible .= $selCol;
                $cv = 1;
            }
        }
        foreach ($a_cabeceras as $Cabecera) {
            $visible = true;
            if (is_array($Cabecera)) {
                $name = self::scalarString($Cabecera['name'] ?? '');
                $name_idx = str_replace(' ', '', $name);
                $id = self::cabeceraFieldKey($Cabecera, 'id') ?? $name_idx;
                $field = self::cabeceraFieldKey($Cabecera, 'field') ?? $name_idx;
                $title = self::scalarString($Cabecera['title'] ?? $name);
                $toolTip = ', toolTip: ' . self::jsQuotedString($title);
                $class = !empty($Cabecera['class']) ? ', cssClass: ' . self::jsQuotedString(self::scalarString($Cabecera['class'])) : '';
                $sortable = !empty($Cabecera['sortable']) ? self::scalarString($Cabecera['sortable']) : 'true';
                $widthRaw = self::scalarString($Cabecera['width'] ?? '');
                $width = filter_var($widthRaw, FILTER_SANITIZE_NUMBER_INT);
                $width = is_string($width) ? $width : '';
                $formatter = !empty($Cabecera['formatter']) ? self::scalarString($Cabecera['formatter']) : '';
                $vis = $Cabecera['visible'] ?? null;
                if ($vis !== null && $vis !== '' && strtolower(self::scalarString($vis)) === 'no') {
                    $visible = false;
                }
                $sDefCol = 'id: ' . self::jsQuotedString($id)
                    . ', name: ' . self::jsQuotedString($name)
                    . ', field: ' . self::jsQuotedString($field)
                    . ', sortable: ' . $sortable . $class . $toolTip;

                $prefWidth = self::slickgridDimension($aColsWidth[$name_idx] ?? null);
                if ($prefWidth !== null) {
                    $sDefCol .= ', width: ' . $prefWidth;
                } elseif ($width !== '') {
                    $sDefCol .= ', width: ' . $width;
                }

                if ($formatter !== '') {
                    $sDefCol .= ', formatter: ' . $formatter;
                }
                $sDefCol = '{' . $sDefCol . '}';
                $aFields[] = $field;
            } else {
                $name = self::scalarString($Cabecera);
                $name_idx = str_replace(' ', '', $name);
                $toolTip = ', toolTip: ' . self::jsQuotedString($name);
                $sDefCol = '{id: ' . self::jsQuotedString($name_idx)
                    . ', name: ' . self::jsQuotedString($name)
                    . ', field: ' . self::jsQuotedString($name_idx)
                    . ', sortable: true' . $toolTip;
                $prefWidth = self::slickgridDimension($aColsWidth[$name_idx] ?? null);
                if ($prefWidth !== null) {
                    $sDefCol .= ', width: ' . $prefWidth;
                }
                $sDefCol .= '}';
                $aFields[] = $name_idx;
            }
            if (($aColsVisible !== null && !empty($aColsVisible[$name_idx]) && ($aColsVisible[$name_idx] === 'true')) || $aColsVisible === null) {
                if (!$visible) continue;
                if ($cv > 0) {
                    $sColumnsVisible .= ',';
                }
                $sColumnsVisible .= $sDefCol;
                $cv++;
            }
            if ($c > 0) {
                $sColumns .= ',';
            }
            $sColumns .= $sDefCol;
            if ($cf > 0) {
                $sColFilters .= ',';
            }
            $sColFilters .= self::jsQuotedString($name_idx);
            $cf++;
            $c++;
        }
        $sColumns .= ']';
        $sColumnsVisible .= ']';
        $sColFilters .= ']';

        $ahora = date("Hms");
        $f = 1;
        $aFilas = [];
        foreach ($a_valores as $num_fila => $fila) {
            if (!is_array($fila)) {
                continue;
            }
            $f++;
            $id_fila = $f . $ahora;
            ksort($fila);
            $icol = 0;
            $aFilas[$num_fila]["id"] = $id_fila;
            foreach ($fila as $col => $valor) {
                if ($col === "clase") {
                    $aFilas[$num_fila]["clase"] = self::scalarString($valor);
                    continue;
                }
                if ($col === "order" || $col === "select") {
                    continue;
                }
                if ($col === "sel") {
                    if (!$showSelCol) {
                        continue;
                    }
                    $chk = '';
                    if (is_array($valor)) {
                        $chk = !empty($valor['select']) ? self::scalarString($valor['select']) : '';
                        $id = self::scalarString($valor['id'] ?? '');
                    } else {
                        $id = self::scalarString($valor);
                    }
                    if ($id !== '') {
                        $chk = in_array($id, $a_valores_chk, true) ? 'checked' : $chk;
                        $aFilas[$num_fila]["sel"] = $chk . '#' . $id;
                    } else {
                        $aFilas[$num_fila]["sel"] = '';
                    }
                } else {
                    if (is_array($valor) && $valor !== []) {
                        $val = self::scalarString($valor['valor'] ?? '');
                        if (!empty($valor['clase'])) {
                            $aFilas[$num_fila]['clase'] = self::scalarString($valor['clase']);
                        }
                        if (!empty($valor['ira'])) {
                            $aFilas[$num_fila]['ira'] = self::scalarString($valor['ira']);
                        }
                        if (!empty($valor['ira2'])) {
                            $aFilas[$num_fila]['ira2'] = self::scalarString($valor['ira2']);
                        }
                        if (!empty($valor['ira3'])) {
                            $aFilas[$num_fila]['ira3'] = self::scalarString($valor['ira3']);
                        }
                        if (!empty($valor['script'])) {
                            $aFilas[$num_fila]['script'] = self::scalarString($valor['script']);
                        }
                        if (!empty($valor['script2'])) {
                            $aFilas[$num_fila]['script2'] = self::scalarString($valor['script2']);
                        }
                        if (!empty($valor['script3'])) {
                            $aFilas[$num_fila]['script3'] = self::scalarString($valor['script3']);
                        }
                        if (!empty($valor['span'])) {
                            $span = (int) self::scalarString($valor['span']);
                            if (isset($aFields[$icol])) {
                                $aFilas[$num_fila][$aFields[$icol]] = $val;
                                $icol++;
                                for ($s = 1; $s < $span; $s++) {
                                    if (isset($aFields[$icol])) {
                                        $aFilas[$num_fila][$aFields[$icol]] = '';
                                    }
                                    $icol++;
                                }
                                $icol--;
                            }
                        } else {
                            if (isset($aFields[$icol])) {
                                $aFilas[$num_fila][$aFields[$icol]] = $val;
                                $icol++;
                            } elseif (!is_numeric($col)) {
                                $aFilas[$num_fila][$col] = $val;
                            }
                        }
                    } else {
                        // Filas asociativas (field => valor): mapear por nombre de columna.
                        // ksort() ordena alfabéticamente; la asignación solo con $aFields[$icol] dejaba casi todo vacío.
                        if (!is_numeric($col) && in_array($col, $aFields, true)) {
                            $aFilas[$num_fila][$col] = ($valor === '' || $valor === null) ? '' : self::scalarString($valor);
                        } elseif (isset($aFields[$icol])) {
                            $aFilas[$num_fila][$aFields[$icol]] = ($valor === '' || $valor === null) ? '' : self::scalarString($valor);
                        } elseif (!is_numeric($col)) {
                            $aFilas[$num_fila][$col] = ($valor === '' || $valor === null) ? '' : self::scalarString($valor);
                        }
                        if (is_numeric($col)) {
                            $icol++;
                        }
                    }
                }
            }
        }

        $f = count($aFilas);
        $rowsForJson = [];
        foreach ($aFilas as $fila) {
            $row = [];
            foreach ($fila as $camp => $valor) {
                $remove = ["\r\n", "\n", "\r"];
                $row[self::scalarString($camp)] = str_replace($remove, ' ', self::scalarString($valor));
            }
            $rowsForJson[] = $row;
        }
        $sData = json_encode(
            $rowsForJson,
            JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS
        );
        if ($sData === false) {
            $sData = '[]';
        }

        if (($grid_height === '' || $grid_height === '0') && $f < 12) {
            $grid_height = (string) ((3 + $f) * 25);
            $grid_height = ((int) $grid_height < 200) ? '200' : $grid_height;
        } elseif ($grid_height === '' || $grid_height === '0') {
            $grid_height = '350';
        }

        $tt = "<input class=\"scroll_id\" id=\"scroll_id_$id_tabla\" name=\"scroll_id_$id_tabla\" data-tabla=\"$id_tabla\" value=\"$scroll_id\" type=\"hidden\">";
        $tt .= "
			<script>
			
			var dataView_$id_tabla;
			var grid_$id_tabla;
			var columns_$id_tabla = $sColumnsVisible;
			var columnsAll_$id_tabla = $sColumns;
			var data_$id_tabla = $sData;

            var resizer = null;
            if (window.Slick && Slick.Plugins && Slick.Plugins.Resizer) {
              resizer = new Slick.Plugins.Resizer({
                container: '#GridContainer_$id_tabla',
                rightPadding: 0,
                bottomPadding: 0,
                minHeight: 80,
                minWidth: 200,
                maxWidth: $grid_width,
                maxHeight: $grid_height,
                calculateAvailableSizeBy:  'window',
              });
            }
 
			var options = {
                enableAutoResize: !!resizer
                ,enableCellNavigation: true
                ,enableAddRow: false
                ,enableColumnReorder: " . ($this->bMultiSort ? 'false' : 'true') . "
                ,multiColumnSort: " . ($this->bMultiSort ? 'true' : 'false') . "
                ,topPanelHeight: 50
                ,autoHeight: false
                ,syncColumnCellResize: true
                ,autosizeColumns: true
                ,autosizeColsMode: Slick.GridAutosizeColsMode.LegacyForceFit
			};
            
			var sortcol = \"" . $sortcol . "\";
			var sortdir = 1;
			var searchString = \"\";
			var columnFilters_$id_tabla = $sColumns;
			
			function metadata(previousItemMetadata) {
                return (rowNumber) => {
                    const item = dataView_$id_tabla.getItem(rowNumber);
                    
                    let meta = {
                        cssClasses: ''
                      };
                    if (typeof previousItemMetadata === 'object') {
                        meta = previousItemMetadata(rowNumber);
                    }

                    if (meta && item && item.clase) {
                        meta.cssClasses = (meta.cssClasses || '') + ' ' + item.clase;
                    }

                    return meta;
				};
			}
			
			function add_scroll_id(row) {
				$(\"#scroll_id_$id_tabla\").val(row);
			}
    
			function clickFormatter(row, cell, value, columnDef, dataContext) {
				if (ira=dataContext['ira']) {
					return \"<span class=link onclick=\\\"fnjs_update_div('#main','\"+ira+\"'); return false; \\\" >\"+value+\"</span>\";
				}
				if (ira=dataContext['script']) {
                    var fun = \"event.stopPropagation(); (function (s) { \"+ira+\"; })(grid_$id_tabla.setSelectedRows([\"+row+\"]))\";
					return \"<span class=link onclick=' \"+fun+\"; return false;' >\"+value+\"</span>\";
				}
				return value;
			}
			function clickFormatter2(row, cell, value, columnDef, dataContext) {
				if (ira=dataContext['ira2']) {
					return \"<span class=link onclick=\\\"fnjs_update_div('#main','\"+ira+\"'): return false; \\\" >\"+value+\"</span>\";
				}
				if (ira=dataContext['script2']) {
					return \"<span class=link onclick='\"+ira+\"; return false;' >\"+value+\"</span>\";
				}
				return value;
			}
			function clickFormatter3(row, cell, value, columnDef, dataContext) {
				if (ira=dataContext['ira3']) {
					return \"<span class=link onclick=\\\"fnjs_update_div('#main','\"+ira+\"'): return false; \\\" >\"+value+\"</span>\";
				}
				if (ira=dataContext['script3']) {
					return \"<span class=link onclick='\"+ira+\"; return false;' >\"+value+\"</span>\";
				}
				return value;
			}
			function checkboxSelectionFormatter(row, cell, value, columnDef, dataContext) {
				if (value == null || value === \"\") {
				  return \"\";
				} else {
				  var array_val=value.split('#');
				  var chk = array_val[0];
				  if (chk.length) {
				  	chk = 'checked=\"checked\"';
				  }
				  var val = '';
				  $.each(array_val, function(index, value) {
					if (index==0) return true;
					if (index>1) {
						val = val+'#';
					}
						val = val+value.replace(/\\\"/g,\"'\");
				  });
				  var id = '#'+val;
				  return  \"<input class=\\\"sel\\\" type=\\\"checkbox\\\" name=\\\"sel[]\\\" id=\\\"\"+id+\"\\\" value=\\\"\"+val+\"\\\" \"+chk+\">\";
				}
			}
			
			";

        $code_lng = $this->resolveIdiomaCode();
        switch ($code_lng) {
            case 'en_US':
                $fecha_local = "
                    // OJO month is 0 index => restar 1.
					var date_a = new Date(fecha_a[2], fecha_a[0]-1, fecha_a[1], hora_a[0], hora_a[1], hora_a[2]);
					var date_b = new Date(fecha_b[2], fecha_b[0]-1, fecha_b[1], hora_b[0], hora_b[1], hora_b[2]);
                    ";
                break;
            default:
                $fecha_local = "
                    // OJO month is 0 index => restar 1.
					var date_a = new Date(fecha_a[2], fecha_a[1]-1, fecha_a[0], hora_a[0], hora_a[1], hora_a[2]);
					var date_b = new Date(fecha_b[2], fecha_b[1]-1, fecha_b[0], hora_b[0], hora_b[1], hora_b[2]);
                    ";
        }

        $tt .= "
			function myFilter_$id_tabla(item,args) {
				var searchFields = $sColFilters;
				if (args.searchString != \"\") {
					var searchWord = args.searchString.toUpperCase();
					var itemFound = false;
					for (var i = 0; i < searchFields.length; i++) {
						if (item[searchFields[i]] != undefined){
							if (item[searchFields[i]].toUpperCase().indexOf(searchWord) != -1){
								itemFound = true;
							}
						}
					}
					if (itemFound === false){
						return false;
					}
				}
				return true;
			}
			function comparer_values(x, y) {
				var dateformat = /^\d{1,2}(\-|\/|\.)\d{1,2}(\-|\/|\.)\d{2,4}$/;
				var dateTimeFormat = /^\d{1,2}(\-|\/|\.)\d{1,2}(\-|\/|\.)\d{2,4} \d{2}:\d{2}:\d{2}$/;
				
				if ( dateTimeFormat.test(x) && dateTimeFormat.test(y) ) {
					var dateTime_a = x.split(' ');
					var dateTime_b = y.split(' ');
					var fecha_a = dateTime_a[0].split('/');
					var hora_a = dateTime_a[1].split(':');
					var fecha_b = dateTime_b[0].split('/');
					var hora_b = dateTime_b[1].split(':');
";
        $tt .= $fecha_local;
        $tt .= "
					var diff = date_a.getTime()-date_b.getTime();
					return (diff==0?diff:diff/Math.abs(diff));
				}
				if ( dateformat.test(x) && dateformat.test(y) ) {
					var fecha_a = x.split('/');
					var fecha_b = y.split('/');
					var hora_a = [0,0,0];
					var hora_b = [0,0,0];
";
        $tt .= $fecha_local;
        $tt .= "
					var diff = date_a.getTime()-date_b.getTime();
					return (diff==0?diff:diff/Math.abs(diff));
				} else {
					if (isNaN(x) || isNaN(y)) {
						x=x?' '+x:'';
						y=y?' '+y:'';
						x=x.toUpperCase();
						y=y.toUpperCase();
						x=fnjs_sin_acentos(x);
						y=fnjs_sin_acentos(y);
						return (x == y ? 0 : (x > y ? 1 : -1));
					} else {
						int_a=parseInt(x,10);
						int_b=parseInt(y,10);
						return (int_a == int_b ? 0 : (int_a > int_b ? 1 : -1));
					}
				}
			}

			function comparer(a,b) {
				return comparer_values(a[sortcol], b[sortcol]);
			}
			";
        $tt .= "
			function toggleFilterRow_$id_tabla() {
			  if ($(grid_$id_tabla.getTopPanel()).is(\":visible\")) {
				grid_$id_tabla.setTopPanelVisibility(false);
			  } else {
				grid_$id_tabla.setTopPanelVisibility(true);
			  }
			}
			";

        $tt .= "
			$(\".grid-header .ui-icon\")
				.addClass(\"ui-state-default ui-corner-all\")
				.on(\"mouseover\", function (e) {
				  $(e.target).addClass(\"ui-state-hover\")
				})
				.on(\"mouseout\", function (e) {
				  $(e.target).removeClass(\"ui-state-hover\")
				});
				
				
			$(function () {
				var initGrid_$id_tabla = function () {
				dataView_$id_tabla = new Slick.Data.DataView();
				grid_$id_tabla = new Slick.Grid(\"#grid_$id_tabla\", dataView_$id_tabla, columns_$id_tabla, options);
				grid_$id_tabla.setSelectionModel(new Slick.RowSelectionModel());
				grid_$id_tabla.registerPlugin(new Slick.AutoTooltips());
				if (resizer) {
				  grid_$id_tabla.registerPlugin(resizer);
				}
				
				var pager = new Slick.Controls.Pager(dataView_$id_tabla, grid_$id_tabla, $(\"#pager\"));
				var columnpicker = new Slick.Controls.ColumnPicker(columnsAll_$id_tabla, grid_$id_tabla, options);
				
				$(\"#inlineFilterPanel_" . $id_tabla . "\")
				  .appendTo(grid_$id_tabla.getTopPanel())
				  .show();
				  
				dataView_$id_tabla.getItemMetadata = metadata(dataView_$id_tabla.getItemMetadata);
				
				grid_$id_tabla.onClick.subscribe(function (e,args) {
					add_scroll_id(args.row);
					grid_$id_tabla.setSelectedRows([args.row]);
				});
				
				grid_$id_tabla.onSelectedRowsChanged.subscribe(function (e,args) {
					$.when($(\"input:checkbox\").prop('checked', false));
					$.when($(\".selected input:checkbox\").prop('checked', true));
				});
				
				grid_$id_tabla.onCellChange.subscribe(function (e, args) {
					dataView_$id_tabla.updateItem(args.item.id, args.item);
				});
				
				grid_$id_tabla.onAddNewRow.subscribe(function (e, args) {
					var item = {\"num\": data_$id_tabla.length, \"id\": \"new_\" + (Math.round(Math.random() * 10000)), \"title\": \"New task\", \"duration\": \"1 day\", \"percentComplete\": 0, \"start\": \"01/01/2009\", \"finish\": \"01/01/2009\", \"effortDriven\": false};
					$.extend(item, args.item);
					dataView_$id_tabla.addItem(item);
				});
				
				grid_$id_tabla.onKeyDown.subscribe(function (e) {
				  if (e.which != 65 || !e.ctrlKey) {
					return false;
				  }
				  
				  var rows = [];
				  for (var i = 0; i < dataView_$id_tabla.getLength(); i++) {
					rows.push(i);
				  }
				  
				  grid_$id_tabla.setSelectedRows(rows);
				  e.preventDefault();
				});
				
				grid_$id_tabla.onSort.subscribe(function (e, args) {
";
        if ($this->bMultiSort) {
            $tt .= "
                    var cols = args.sortCols;
                    dataView_$id_tabla.sort(function (dataRow1, dataRow2) {
                        for (var i = 0, l = cols.length; i < l; i++) {
                          var field = cols[i].sortCol.field;
                          var sign = cols[i].sortAsc ? 1 : -1;
                          var value1 = dataRow1[field], value2 = dataRow2[field];
                          
                          var result = comparer_values(value1, value2) * sign;
                          if (result != 0) {
                            return result;
                          }
                        }
                        return 0;
                    });
                    grid_$id_tabla.invalidate();
                    grid_$id_tabla.render();
";
        } else {
            $tt .= "
					sortdir = args.sortAsc ? 1 : -1;
					sortcol = args.sortCol.field;
					
					dataView_$id_tabla.sort(comparer, args.sortAsc);
";
        }
        $tt .= "
				});
				dataView_$id_tabla.onRowCountChanged.subscribe(function (e, args) {
					grid_$id_tabla.updateRowCount();
					grid_$id_tabla.render();
				});
				
				dataView_$id_tabla.onRowsChanged.subscribe(function (e, args) {
					grid_$id_tabla.invalidateRows(args.rows);
					grid_$id_tabla.render();
				});
				
				dataView_$id_tabla.onPagingInfoChanged.subscribe(function (e, pagingInfo) {
					var isLastPage = pagingInfo.pageSize * (pagingInfo.pageNum + 1) - 1 >= pagingInfo.totalRows;
					var enableAddRow = isLastPage || pagingInfo.pageSize == 0;
					var options = grid_$id_tabla.getOptions();
					
					if (options.enableAddRow != enableAddRow) {
					  grid_$id_tabla.setOptions({enableAddRow: enableAddRow});
					}
				});
				
				$(\"#txtSearch_" . $id_tabla . "\").on(\"keydown\", function (e) {
				    if (e.keyCode == 13) {
					  return false;
				    }
                });
				$(\"#txtSearch_" . $id_tabla . "\").on(\"keyup\", function (e) {
					Slick.GlobalEditorLock.cancelCurrentEdit();
					if (e.which == 27) {
					  this.value = \"\";
					}
					searchString = this.value;
					updateFilter();
				});
				
				function updateFilter() {
					dataView_$id_tabla.setFilterArgs({
						searchString: searchString
					});
					dataView_$id_tabla.refresh();
				}
			";

        if ($sortcol) {
            $tt .= " data_$id_tabla.sort(comparer); ";
        }

        $tt .= "
            var base = $('#main').attr('refe');
            var savedState = fnjs_recuperar_estado(base, '$id_tabla');
            var backendHasSel = false;
            if (savedState) {
                var scroll_id_input = $('#scroll_id_$id_tabla').val();
                if ((scroll_id_input == 0 || scroll_id_input == '') && savedState.scroll_id) {
                    $('#scroll_id_$id_tabla').val(savedState.scroll_id);
                }
                if (savedState.sel && savedState.sel.length > 0) {
                    for (var i=0; i<data_{$id_tabla}.length; i++) {
                        if (data_{$id_tabla}[i].sel && data_{$id_tabla}[i].sel.indexOf('checked') !== -1) {
                            backendHasSel = true;
                            break;
                        }
                    }
                    if (!backendHasSel) {
                        for (var i=0; i<data_{$id_tabla}.length; i++) {
                            var rowSel = data_{$id_tabla}[i].sel;
                            if (rowSel) {
                                var parts = rowSel.split('#');
                                var id = parts.slice(1).join('#');
                                if (savedState.sel.indexOf(id) !== -1) {
                                    data_{$id_tabla}[i].sel = 'checked#' + id;
                                }
                            }
                        }
                    }
                }
            }

			dataView_$id_tabla.beginUpdate();
			dataView_$id_tabla.setItems(data_$id_tabla);
			dataView_$id_tabla.setFilterArgs({
				searchString: searchString
			});
			dataView_$id_tabla.setFilter(myFilter_$id_tabla);
			dataView_$id_tabla.endUpdate();
			$(\"#grid_$id_tabla\").resizable({
			  handles: 'se',
			  resize: function (event, ui) {
			    grid_$id_tabla.resizeCanvas();
			  },
			  stop: function (event, ui) {
			    var w = Math.round(ui.size.width);
			    var h = Math.round(ui.size.height);
			    $('#GridContainer_$id_tabla').css('max-width', w + 'px');
			    if (resizer) {
			      resizer.setOptions({ maxWidth: w, maxHeight: h });
			    }
			    grid_$id_tabla.resizeCanvas();
			  }
			});
		";

        if ($showSelCol) {
            $tt .= " 
                setTimeout(function() {
                    var scroll_id_final = $('#scroll_id_$id_tabla').val();
                    var rowsToSelect = [];

                    if (savedState && savedState.sel && savedState.sel.length > 0 && !backendHasSel) {
                        var totalItems = dataView_{$id_tabla}.getLength();
                        for (var i=0; i<totalItems; i++) {
                            var item = dataView_{$id_tabla}.getItem(i);
                            if (item && item.sel) {
                                var parts = item.sel.split('#');
                                var id = parts.slice(1).join('#');
                                if (savedState.sel.indexOf(id) !== -1) {
                                    rowsToSelect.push(i);
                                }
                            }
                        }
                    }

                    if (rowsToSelect.length === 0) {
                        var totalItemsBackend = dataView_{$id_tabla}.getLength();
                        for (var j=0; j<totalItemsBackend; j++) {
                            var rowItem = dataView_{$id_tabla}.getItem(j);
                            if (rowItem && rowItem.sel && rowItem.sel.indexOf('checked') === 0) {
                                rowsToSelect.push(j);
                            }
                        }
                    }

                    if (rowsToSelect.length > 0) {
                        grid_{$id_tabla}.setSelectedRows(rowsToSelect);
                        grid_{$id_tabla}.scrollRowIntoView(rowsToSelect[0]);
                    } else if (scroll_id_final > 0 && scroll_id_final < data_{$id_tabla}.length) {
                        grid_{$id_tabla}.scrollRowToTop(scroll_id_final);
                    }
                }, 200);
            ";
        }
        if ($bPanelVis) {
            $tt .= "toggleFilterRow_$id_tabla();";
        }

		$tt .= "
			$('#grid_$id_tabla').css({ width: '{$grid_width}px', height: '{$grid_height}px' });
			$('#GridContainer_$id_tabla').css('max-width', '{$grid_width}px');
			grid_$id_tabla.resizeCanvas();
				};
				if (typeof fnjs_ensureSlickLista === 'function') {
					fnjs_ensureSlickLista(initGrid_$id_tabla);
				} else {
					initGrid_$id_tabla();
				}
		  })
		</script>
		";

        $colVisIcon = '';
        if ($this->bColVis) {
            $colVisIcon = '<span style="float:right" class="ui-icon ui-icon-disk" title="' . _("guardar selección de columnas") . '"
				onclick="fnjs_def_tabla(\'' . $id_tabla . '\')"></span>';
        }
        $filtroIcon = '';
        if ($this->bFiltro) {
            $filtroIcon = '<span style="float:right" class="ui-icon ui-icon-search" title="' . _("ver/ocultar panel de búsqueda") . '"
				onclick="toggleFilterRow_' . $id_tabla . '()"></span>';
        }
        $ta = '<div id="GridContainer_' . $id_tabla . '" style="width:100%; max-width:' . $grid_width . 'px; height:auto;">
		<div class="grid-header">
          <span style="width:90%; display: inline-block;">' . $botones . '</span>
		  ' . $colVisIcon . '
		  ' . $filtroIcon . '
		</div>
		<div id="grid_' . $id_tabla . '" style="width:' . $grid_width . 'px; height:' . $grid_height . 'px;"></div>
		';
        $ta .= "</div>";

        $ta .= "
		<div id=\"inlineFilterPanel_" . $id_tabla . "\" style=\"background:#dddddd;padding:3px;color:black;\">
		  " . _("Buscar en todas las columnas") . " <input type=\"text\" id=\"txtSearch_" . $id_tabla . "\">
		</div>
		";

        return $ta . $tt;
    }

    /**
     * Versión HTML puro (sin SlickGrid).
     */
    public function mostrar_tabla_html(): string
    {
        $a_botones = $this->aBotones;
        $a_cabeceras = $this->aCabeceras;
        $a_valores = $this->aDatos;
        $id_tabla = $this->sid_tabla;

        $botones = "";
        $cabecera = "";
        $tbody = "";
        $tt = "";
        $numBotones = 0;
        if ($a_valores === []) {
            return _("no hay ninguna fila");
        }
        foreach ($a_botones as $a_boton) {
            $prefix = empty($a_boton['prefix']) ? '' : self::scalarString($a_boton['prefix']) . '_';
            $btn = $prefix . 'btn' . $numBotones++;
            $botones .= '<INPUT id="' . $btn . '" name="' . $btn . '" type=button value="' . self::scalarString($a_boton['txt'] ?? '') . '" onClick=\'' . self::scalarString($a_boton['click'] ?? '') . '\'>';
        }
        if ($numBotones > 0) {
            $botones .= '</td></tr>';
        }
        if ($this->botones_grupo) {
            $numBotones = 5;
        }
        $showSelCol = ($numBotones > 0) || $this->bConSel;

        $cab = 1;
        $aColsVisible = [];
        $num_col = 0;
        foreach ($a_cabeceras as $Cabecera) {
            $meta = self::cabeceraHtmlMeta($Cabecera);
            $aColsVisible[$num_col] = $meta['visible'];
            $num_col++;

            if ($meta['visible']) {
                if ($meta['name'] !== '') {
                    $cabecera .= '<th class=cabecera ' . $meta['width'] . ' ' . $meta['class'] . ' >' . trim($meta['name']) . "</th>\n";
                } else {
                    $cabecera .= '<th class=cabecera tipo=\'notext\' ' . $meta['width'] . ' ' . $meta['class'] . " ></th>\n";
                }
                $cab++;
            }
        }
        if ($showSelCol) {
            $cabecera = "<th class=cabecera tipo='notext' width='20' ></th>\n" . $cabecera;
            $cab++;
        }
        $cabecera .= "</tr>\n";
        $ahora = date("Hms");
        $f = 1;
        $scroll_id = !empty($a_valores['scroll_id']) ? self::scalarString($a_valores['scroll_id']) : '0';
        $tt .= '<!-- DEBUG HTML TABLE: id=' . $id_tabla . ', num_headers=' . count($a_cabeceras) . ', b=' . $numBotones . " -->\n";

        $a_valores_chk = self::normalizeSelectedIds($a_valores['select'] ?? null);
        unset($a_valores['select']);

        unset($a_valores['scroll_id']);
        foreach ($a_valores as $num_fila => $fila) {
            if (!is_array($fila)) {
                continue;
            }
            $clase = "imp";
            $f % 2 ? 0 : $clase = "par";
            $f++;
            $id_fila = $f . $ahora;
            if (!empty($fila['clase'])) {
                $clase .= ' ' . self::scalarString($fila['clase']);
            }
            $filaJson = json_encode($fila);
            $tbody .= "<tr id='$id_fila' class='$clase' onclick='fnjs_clic_fila(this, event)' data-json='" . htmlspecialchars($filaJson !== false ? $filaJson : '', ENT_QUOTES, 'UTF-8') . "'>";

            if ($showSelCol) {
                if (isset($fila['sel'])) {
                    $valor = $fila['sel'];
                    $chk = '';
                    if (is_array($valor)) {
                        $chk = !empty($valor['select']) ? self::scalarString($valor['select']) : '';
                        $id = self::scalarString($valor['id'] ?? '');
                    } else {
                        $id = self::scalarString($valor);
                    }
                    if ($id !== '') {
                        if ($a_valores_chk !== []) {
                            $chk = in_array($id, $a_valores_chk, true) ? 'checked' : '';
                        }
                        $tbody .= "<td tipo='sel' title='" . _("clic para seleccionar") . "'>";
                        $tbody .= '<input class=\'sel\' type=\'checkbox\' ' . $chk . "  name='sel[]' id='a" . $id . "' value='" . $id . "'>";
                        $tbody .= "</td>";
                    } else {
                        $tbody .= "<td></td>";
                    }
                } else {
                    $tbody .= "<td></td>";
                }
            }

            $tbody .= self::renderCeldasDatos($fila, $a_cabeceras, $aColsVisible, self::resolveStartIcol($fila));
            $tbody .= "</tr>\n";
        }

        if ($numBotones > 0) {
            $botones = "<tr class=botones><td colspan='$cab'>" . $botones;
        }
        $tt = "<input class=\"scroll_id\" id=\"scroll_id_$id_tabla\" name=\"scroll_id_$id_tabla\" data-tabla=\"$id_tabla\" value=\"$scroll_id\" type=\"hidden\">";
        $tt .= "<table>$botones</table>\n";
        $tt .= "<table border=1  class='sortable' id='$id_tabla'>\n";
        $tt .= "<thead><tr>";
        $tt .= "$cabecera</thead><tbody>";
        $tt .= $tbody;
        $tt .= "</tbody></table>\n";
        $tt .= "<script>
			$(document).ready(function() {
                var base = $('#main').attr('refe');
                var savedState = fnjs_recuperar_estado(base, '$id_tabla');
                if (savedState && savedState.sel) {
                    var backendHasSel = $('input:checked').length > 0;
                    if (!backendHasSel) {
                        savedState.sel.forEach(function(id) {
                            var escapedId = id.replace(/([ #;?%&,.+*~\':\"!^$[\]()=>|\/@])/g, '\\\\$1');
                            var checkbox = $('#a' + escapedId);
                            if (checkbox.length) {
                                checkbox.prop('checked', true);
                            }
                        });
                    }
                }

				var h = $('input:checked');
				if (h.length) {
					var h = (h.offset().top) - 300;
					$('#main').scrollTop(h);
				}
			});
			</script>";

        return $tt;
    }

    public function text_first(mixed $a, mixed $b): int
    {
        if (is_numeric($a)) {
            if (is_numeric($b)) {
                return $a === $b ? 0 : (($a < $b) ? -1 : 1);
            }
            return 1;
        }
        if (is_numeric($b)) {
            return -1;
        }
        return strcasecmp(self::scalarString($a), self::scalarString($b));
    }

    public function getCsv(string $filename): void
    {
        $a_valores = $this->aDatos;

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
        header("Content-Transfer-Encoding: binary");

        $fp = fopen('php://output', 'w');
        if ($fp === false) {
            return;
        }
        foreach ($a_valores as $num_fila => $fila) {
            if (!is_array($fila)) {
                continue;
            }
            /** @var list<string> $a_valores_simple */
            $a_valores_simple = [];
            uksort($fila, [$this, 'text_first']);
            foreach ($fila as $col => $valor) {
                if ($col === "clase" || $col === "order" || $col === "select") {
                    continue;
                }
                if ($col === "sel") {
                    $id = is_array($valor) ? self::scalarString($valor['id'] ?? '') : self::scalarString($valor);
                    if ($id !== '') {
                        $a_valores_simple[] = $id;
                    }
                } elseif (is_array($valor)) {
                    $a_valores_simple[] = self::scalarString($valor['valor'] ?? '');
                } else {
                    $text = self::scalarString($valor);
                    $fechaIso = self::formatFechaIsoFromDmy($text);
                    $a_valores_simple[] = $fechaIso ?? $text;
                }
            }
            fputcsv($fp, $a_valores_simple, "\t", '"');
        }
        fclose($fp);
    }

    /** @param list<string>|array<int|string, string> $aGrupos */
    public function setGrupos(array $aGrupos): void
    {
        $this->aGrupos = $aGrupos;
    }

    /** @param list<array<string, mixed>|string> $aCabeceras */
    public function setCabeceras(array $aCabeceras): void
    {
        $this->aCabeceras = $aCabeceras;
    }

    public function setPie(string $str): void
    {
        $this->sPie = $str;
    }

    public function getMultiSort(): bool
    {
        return $this->bMultiSort;
    }

    public function setMultiSort(bool $bMultiSort): void
    {
        $this->bMultiSort = $bMultiSort;
    }

    public function setSortCol(string $ssortcol): void
    {
        $this->ssortCol = str_replace(' ', '', $ssortcol);
    }

    /** @param array<string, bool|string> $aColVisible */
    public function setColVisible(array $aColVisible): void
    {
        $this->aColVisible = $aColVisible;
    }

    /** @param array<int|string, mixed> $aDatos */
    public function setDatos(array $aDatos): void
    {
        $this->aDatos = $aDatos;
    }

    /** @param list<array<string, mixed>> $aBotones */
    public function setBotones(array $aBotones): void
    {
        $this->aBotones = $aBotones;
    }

    public function setConSel(bool $bConSel): void
    {
        $this->bConSel = $bConSel;
    }

    public function setId_tabla(string $sid_tabla): void
    {
        $this->sid_tabla = $sid_tabla;
    }

    public function setFiltro(bool $bFiltro): void
    {
        $this->bFiltro = $bFiltro;
    }

    public function setColVis(bool $bColVis): void
    {
        $this->bColVis = $bColVis;
    }

    public function setFormatoTabla(string $formatoTabla): void
    {
        $this->formato_tabla = $formatoTabla;
    }
}
