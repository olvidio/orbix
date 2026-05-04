<?php

namespace frontend\shared\web;

use frontend\shared\PostRequest;
use src\shared\config\ConfigGlobal;
use function frontend\shared\helpers\is_true;

/**
 * Classe per gestionar llistes de dades tipus taula (html o SlickGrid).
 */
class Lista
{
    private string $sNombre;
    private int $ikey;
    private array $aGrupos = [];
    private bool $botones_grupo = false;
    private array $aCabeceras = [];
    private string $sPie = '';
    private string $ssortCol = '';
    private array $aColVisible = [];
    private array $aDatos = [];
    private array $aBotones = [];
    private string $sid_tabla = 'uno';
    private bool $bFiltro = true;
    private bool $bColVis = true;
    private string $formato_tabla = '';
    private bool $bMultiSort = false;

    public function __construct()
    {
    }

    /**
     * Llama al endpoint backend que devuelve las preferencias de tabla para
     * el usuario actual. Se usa para decidir HTML vs SlickGrid y, para
     * SlickGrid, recuperar colVisible/colWidths/tamaños.
     */
    private function fetchPreferenciaTabla(string $id_tabla = ''): array
    {
        if (ConfigGlobal::is_test_mode()) {
            return ['formato_tabla' => '', 'slickgrid' => null];
        }
        $data = PostRequest::getDataFromUrl(
            '/src/usuarios/preferencia_tabla_get',
            ['id_tabla' => $id_tabla]
        );
        if (!is_array($data)) {
            return ['formato_tabla' => '', 'slickgrid' => null];
        }
        return [
            'formato_tabla' => (string)($data['formato_tabla'] ?? ''),
            'slickgrid' => is_array($data['slickgrid'] ?? null) ? $data['slickgrid'] : null,
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
            $class = '';
            $width = '';
            $visible = true;
            if (is_array($Cabecera)) {
                $name = $Cabecera['name'];
                if (!empty($Cabecera['class'])) {
                    $class = "class=\"{$Cabecera['class']}\"";
                }
                if (!empty($Cabecera['width'])) {
                    $width = "width=\"{$Cabecera['width']}\"";
                }
                if (!empty($Cabecera['visible']) && strtolower($Cabecera['visible']) === 'no') {
                    $visible = false;
                }
            } else {
                $name = $Cabecera;
            }

            $aColsVisible[$num_col] = $visible;
            $num_col++;

            if ($visible) {
                if (!empty($name)) {
                    $cabecera .= "<th class=cabecera $width $class >" . trim($name) . "</th>\n";
                } else {
                    $cabecera .= "<th class=cabecera tipo='notext' $width $class ></th>\n";
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
            $clase = "imp";
            $f % 2 ? 0 : $clase = "par";
            $f++;
            if (!empty($fila['clase'])) {
                $clase .= " " . $fila['clase'];
            }
            $tbody .= "<tr class='$clase' >";

            // Heurística para localizar la columna inicial de datos cuando hay IDs en la fila.
            $start_icol = 0;
            if (!isset($fila[0]) && isset($fila[1])) {
                $start_icol = 1;
            } elseif (isset($fila[0])) {
                $is_id = false;
                if (isset($fila['sel'])) {
                    $sel_id = is_array($fila['sel']) ? $fila['sel']['id'] : (string)$fila['sel'];
                    if (strpos($sel_id, (string)$fila[0]) !== false || preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}/i', (string)$fila[0])) {
                        $is_id = true;
                    }
                } elseif (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}/i', (string)$fila[0])) {
                    $is_id = true;
                }

                if ($is_id) {
                    $start_icol = 1;
                }
            }

            $icol_offset = 0;
            foreach ($aCabeceras as $num_col => $Cabecera) {
                if ($aColsVisible[$num_col]) {
                    $field = (is_array($Cabecera) && !empty($Cabecera['field'])) ? $Cabecera['field'] : null;
                    $id = (is_array($Cabecera) && !empty($Cabecera['id'])) ? $Cabecera['id'] : null;

                    $valor = '';
                    if ($field && array_key_exists($field, $fila)) {
                        $valor = $fila[$field];
                    } elseif ($id && array_key_exists($id, $fila)) {
                        $valor = $fila[$id];
                    } else {
                        $target_idx = $start_icol + $icol_offset;
                        $valor = $fila[$target_idx] ?? '';
                        $icol_offset++;
                    }
                    if (is_array($valor)) {
                        $val = $valor['valor'];
                        $tbody .= "<td>";
                        if (!empty($valor['ira'])) {
                            $ira = $valor['ira'];
                            $tbody .= "<span class=\"link\" onclick=\"fnjs_update_div('#main','$ira')\" >$val</span>";
                        } elseif (!empty($valor['script'])) {
                            $ira = $valor['script'];
                            $tbody .= "<span class=\"link\" onclick='$ira' >$val</span>";
                        } else {
                            $tbody .= $val;
                        }

                        for ($idx = 2; $idx <= 3; $idx++) {
                            if (!empty($valor["ira$idx"])) {
                                $ira = $valor["ira$idx"];
                                $tbody .= " <span class=\"link\" onclick=\"fnjs_update_div('#main','$ira')\" >$val</span>";
                            }
                            if (!empty($valor["script$idx"])) {
                                $ira = $valor["script$idx"];
                                $tbody .= " <span class=\"link\" onclick='$ira' >$val</span>";
                            }
                        }

                        $tbody .= "</td>";

                    } else {
                        $valor = $valor ?? '';
                        if (preg_match("/^(\d)+[\/-](\d)+[\/-](\d\d)+$/", $valor)) {
                            [$d, $m, $y] = preg_split('/[:\/.-]/', $valor);
                            $fecha_iso = date("Y-m-d", mktime(0, 0, 0, $m, $d, $y));
                            $tbody .= "<td class='fecha' fecha_iso='$fecha_iso'>$valor</td>";
                        } else {
                            $tbody .= "<td>$valor</td>";
                        }
                    }
                }
            }
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
            $this->aDatos = $aDatos[$key] ?? [];
            $this->ikey = $key;
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
        if (!empty($a_botones)) {
            $botones = '';
            if ($a_botones === "ninguno") {
                $b = 0;
            } else {
                $b = 0;
                foreach ($a_botones as $a_boton) {
                    $prefix = empty($a_boton['prefix']) ? '' : $a_boton['prefix'] . '_';
                    $btn = $prefix . "btn" . $b++;
                    $botones .= "<INPUT id='$btn' name='$btn' type=button value=\"" . $a_boton['txt'] . "\" onClick='" . $a_boton['click'] . "'>";
                }
                $botones .= "</td></tr>";
            }
            $cab = count($this->aCabeceras);
            $botones = "<tr class=botones><td colspan='$cab'>" . $botones;
            if ($b > 0) {
                $this->botones_grupo = true;
                $Html .= "<table>$botones</table>\n";
            }
        }
        $this->setBotones([]);
        foreach ($aGrupos as $key => $titulo) {
            $this->aDatos = $aDatos[$key] ?? [];
            $this->ikey = $key;
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
        $b = 0;
        $scroll_id = !empty($a_valores['scroll_id']) ? $a_valores['scroll_id'] : 0;
        unset($a_valores['scroll_id']);
        if (isset($a_valores['select'])) {
            $a_valores_chk = $a_valores['select'];
            unset($a_valores['select']);
        } else {
            $a_valores_chk = [];
        }
        if (empty($a_valores)) {
            return _("no hay ninguna fila");
        }
        if (!empty($a_botones)) {
            if ($a_botones === "ninguno") {
                $b = "x";
            } else {
                foreach ($a_botones as $a_boton) {
                    $prefix = empty($a_boton['prefix']) ? '' : $a_boton['prefix'] . '_';
                    $btn = $prefix . "btn" . $b++;
                    $botones .= "<INPUT id='$btn' name='$btn' type=button value=\"" . $a_boton['txt'] . "\" onClick='" . $a_boton['click'] . "'>";
                }
            }
        }

        $aColsVisible = '';
        $aColsWidth = [];
        $bPanelVis = false;
        $aPrefs = $this->fetchPreferenciaTabla($id_tabla)['slickgrid'] ?? null;
        if (is_array($aPrefs)) {
            if (!empty($aPrefs['colVisible'])) {
                $aColsVisible = $aPrefs['colVisible'];
            }
            $bPanelVis = ($aPrefs['panelVis'] ?? '') === "si";
            if (!empty($aPrefs['colWidths'])) {
                $aColsWidth = $aPrefs['colWidths'];
            }
            $grid_width = (!empty($aPrefs['widthGrid'])) ? $aPrefs['widthGrid'] : '900';
            $grid_height = (!empty($aPrefs['heightGrid'])) ? $aPrefs['heightGrid'] : 0;
        }

        $c = 0;
        $cv = 0;
        $sColumns = '[';
        $sColumnsVisible = '[';
        $sColFilters = '[';
        $aFields = [];
        if ($b !== 0 || $b === 'x') {
            $c++;
            $width = $aColsWidth['sel'] ?? 30;
            $sColumns .= "{id: \"sel\", name: \"sel\", field: \"sel\", width:$width, sortable: false, formatter: checkboxSelectionFormatter}";
            if (!is_array($aColsVisible) || is_true($aColsVisible['sel'])) {
                $sColumnsVisible .= "{id: \"sel\", name: \"sel\", field: \"sel\", width:$width, sortable: false, formatter: checkboxSelectionFormatter},";
            }
        }
        foreach ($a_cabeceras as $Cabecera) {
            $visible = true;
            if (is_array($Cabecera)) {
                $name = $Cabecera['name'];
                $name_idx = str_replace(' ', '', $name);
                $id = !empty($Cabecera['id']) ? $Cabecera['id'] : str_replace(' ', '', $name);
                $field = !empty($Cabecera['field']) ? $Cabecera['field'] : str_replace(' ', '', $name);
                $toolTip = !empty($Cabecera['title']) ? ", toolTip: \"{$Cabecera['title']}\"" : ", toolTip: \"{$Cabecera['name']}\"";
                $class = !empty($Cabecera['class']) ? ", cssClass: \"{$Cabecera['class']}\"" : '';
                $sortable = !empty($Cabecera['sortable']) ? $Cabecera['sortable'] : 'true';
                $width = !empty($Cabecera['width']) ? $Cabecera['width'] : '';
                $width = filter_var($width, FILTER_SANITIZE_NUMBER_INT);
                $formatter = !empty($Cabecera['formatter']) ? $Cabecera['formatter'] : '';
                if (!empty($Cabecera['visible']) && strtolower($Cabecera['visible'] ?? '') === 'no') {
                    $visible = false;
                }
                $sDefCol = "id: \"$id\", name: \"$name\", field: \"$field\", sortable: $sortable" . $class . $toolTip;

                if (isset($aColsWidth[$name_idx])) {
                    $sDefCol .= ", width: " . $aColsWidth[$name_idx];
                } elseif (!empty($width)) {
                    $sDefCol .= ", width: $width";
                }

                if (!empty($formatter)) $sDefCol .= ", formatter: $formatter";
                $sDefCol = "{" . $sDefCol . "}";
                $aFields[] = $field;
            } else {
                $name = $Cabecera;
                $name_idx = str_replace(' ', '', $Cabecera);
                $toolTip = ", toolTip: \"$name\"";
                $sDefCol = "{id: \"$name_idx\", name: \"$name\", field: \"$name_idx\", sortable: true" . $toolTip;
                if (isset($aColsWidth[$name_idx])) {
                    $sDefCol .= ", width: " . $aColsWidth[$name_idx];
                }
                $sDefCol .= "}";
                $aFields[] = $name_idx;
            }
            if ((is_array($aColsVisible) && !empty($aColsVisible[$name_idx]) && ($aColsVisible[$name_idx] === "true")) || !is_array($aColsVisible)) {
                if (!$visible) continue;
                if ($cv > 0) {
                    $sColumnsVisible .= ',';
                }
                $sColumnsVisible .= $sDefCol;
                $cv++;
            }
            if ($c > 0) {
                $sColumns .= ',';
                $sColFilters .= ',';
            }
            $sColumns .= $sDefCol;
            $sColFilters .= "\"$name_idx\"";
            $c++;
        }
        $sColumns .= ']';
        $sColumnsVisible .= ']';
        $sColFilters .= ']';

        $ahora = date("Hms");
        $f = 1;
        $aFilas = [];
        foreach ($a_valores as $num_fila => $fila) {
            $f++;
            $id_fila = $f . $ahora;
            ksort($fila);
            $icol = 0;
            $aFilas[$num_fila]["id"] = $id_fila;
            foreach ($fila as $col => $valor) {
                if ($col === "clase") {
                    $id = $valor;
                    $aFilas[$num_fila]["clase"] = addslashes($id);
                    continue;
                }
                if ($col === "order" || $col === "select") {
                    continue;
                }
                if ($col === "sel") {
                    if (empty($b)) {
                        continue;
                    }
                    if (is_array($valor)) {
                        $chk = !empty($valor['select']) ? $valor['select'] : "";
                        $id = $valor['id'];
                    } else {
                        $id = $valor;
                    }
                    if (!empty($id)) {
                        $chk = in_array($id, $a_valores_chk) ? 'checked' : '';
                        $aFilas[$num_fila]["sel"] = $chk . '#' . addslashes($id);
                    } else {
                        $aFilas[$num_fila]["sel"] = '';
                    }
                } else {
                    if (is_array($valor) && !empty($valor)) {
                        $val = $valor['valor'];
                        if (!empty($valor['clase'])) {
                            $aFilas[$num_fila]['clase'] = $valor['clase'];
                        }
                        if (!empty($valor['ira'])) {
                            $aFilas[$num_fila]['ira'] = $valor['ira'];
                        }
                        if (!empty($valor['ira2'])) {
                            $aFilas[$num_fila]['ira2'] = $valor['ira2'];
                        }
                        if (!empty($valor['ira3'])) {
                            $aFilas[$num_fila]['ira3'] = $valor['ira3'];
                        }
                        if (!empty($valor['script'])) {
                            $aFilas[$num_fila]['script'] = addslashes($valor['script'] ?? '');
                        }
                        if (!empty($valor['script2'])) {
                            $aFilas[$num_fila]['script2'] = addslashes($valor['script2'] ?? '');
                        }
                        if (!empty($valor['script3'])) {
                            $aFilas[$num_fila]['script3'] = addslashes($valor['script3'] ?? '');
                        }
                        if (!empty($valor['span'])) {
                            $span = $valor['span'];
                            if (isset($aFields[$icol])) {
                                $aFilas[$num_fila][$aFields[$icol]] = addslashes($val ?? '');
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
                                $aFilas[$num_fila][$aFields[$icol]] = addslashes($val ?? '');
                                $icol++;
                            } elseif (!is_numeric($col)) {
                                $aFilas[$num_fila][$col] = addslashes($val ?? '');
                            }
                        }
                    } else {
                        if (isset($aFields[$icol])) {
                            $aFilas[$num_fila][$aFields[$icol]] = ($valor === '' || $valor === null) ? '' : addslashes($valor ?? '');
                        } elseif (!is_numeric($col)) {
                            $aFilas[$num_fila][$col] = ($valor === '' || $valor === null) ? '' : addslashes($valor ?? '');
                        }
                        if (is_numeric($col)) {
                            $icol++;
                        }
                    }
                }
            }
        }

        $f = 0;
        $sData = '[';
        foreach ($aFilas as $num_fila => $fila) {
            $f++;
            if ($f > 1) $sData .= ',';
            $c = 0;
            $sData .= '{';
            foreach ($fila as $camp => $valor) {
                $c++;
                if ($c > 1) $sData .= ',';
                $remove = ["\r\n", "\n", "\r"];
                $valor = str_replace($remove, ' ', $valor);
                $sData .= "\"$camp\": \"$valor\"";
            }
            $sData .= '}';
        }
        $sData .= ']';

        if (empty($grid_height) && $f < 12) {
            $grid_height = (3 + $f) * 25;
            $grid_height = ($grid_height < 200) ? 200 : $grid_height;
        } else {
            $grid_height = empty($grid_height) ? 350 : $grid_height;
        }

        $tt = "<input class=\"scroll_id\" id=\"scroll_id_$id_tabla\" name=\"scroll_id_$id_tabla\" data-tabla=\"$id_tabla\" value=\"$scroll_id\" type=\"hidden\">";
        $tt .= "
			<script>
			
			var dataView_$id_tabla;
			var grid_$id_tabla;
			var columns_$id_tabla = $sColumnsVisible;
			var columnsAll_$id_tabla = $sColumns;
			var data_$id_tabla = $sData;

            var resizer = new Slick.Plugins.Resizer({
              container: '#grid_$id_tabla',
              rightPadding: 5,
              bottomPadding: 5,
              minHeight: 80,
              minWidth: 200,
              calculateAvailableSizeBy:  'container',
            });
 
			var options = {
                enableAutoResize: true
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
            
			function resumeAutoResize() {
              resizer.pauseResizer(false);
              resizer.resizeGrid();
              resizer.resizeGrid(500);
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

        $idioma = $_SESSION['session_auth']['idioma'];
        if (!isset($idioma)) {
            $idioma = $_SESSION['oConfig']->getIdioma_default();
        }
        $a_idioma = explode('.', $idioma);
        $code_lng = $a_idioma[0];
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
				dataView_$id_tabla = new Slick.Data.DataView();
				grid_$id_tabla = new Slick.Grid(\"#grid_$id_tabla\", dataView_$id_tabla, columns_$id_tabla, options);
				grid_$id_tabla.setSelectionModel(new Slick.RowSelectionModel());
				grid_$id_tabla.registerPlugin(new Slick.AutoTooltips());
				grid_$id_tabla.registerPlugin(resizer);
				
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
			$(\"#grid_$id_tabla\").resizable();
		";

        if (isset($scroll_id)) {
            $tt .= " 
                setTimeout(function() {
                    var scroll_id_final = $('#scroll_id_$id_tabla').val();
                    
                    var rowsToSelect = [];
                    if (savedState && savedState.sel && !backendHasSel) {
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
                        if (rowsToSelect.length > 0) {
                            grid_{$id_tabla}.setSelectedRows(rowsToSelect);
                        }
                    }

                    if (rowsToSelect.length > 0) {
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
			var container = $(grid_$id_tabla.getContainerNode());
			var h_header =  $('.grid-header').height();
			var vph = $grid_height - h_header;
			container.height(vph);
			grid_$id_tabla.resizeCanvas();
		  })
		</script>
		";

        $ta = "<div id=\"GridContainer_" . $id_tabla . "\"  style=\"width:{$grid_width}px; height:auto;\" >
		<div class=\"grid-header\">
          <span style=\"width:90%; display: inline-block;\">$botones</span>
		  <span style=\"float:right\" class=\"ui-icon ui-icon-disk\" title=\"" . _("guardar selección de columnas") . "\"
				onclick=\"fnjs_def_tabla('" . $id_tabla . "')\"></span>
		  <span style=\"float:right\" class=\"ui-icon ui-icon-search\" title=\"" . _("ver/ocultar panel de búsqueda") . "\"
				onclick=\"toggleFilterRow_$id_tabla()\"></span>
		</div>
		<div id=\"grid_$id_tabla\"  style=\"width:{$grid_width}px; height:{$grid_height}px;\" onresize=\"resumeAutoResize()\" ></div>
		";
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
        $b = 0;
        if (empty($a_valores)) {
            return _("no hay ninguna fila");
        }
        if (!empty($a_botones)) {
            if ($a_botones === "ninguno") {
                $b = "x";
            } else {
                foreach ($a_botones as $a_boton) {
                    $prefix = empty($a_boton['prefix']) ? '' : $a_boton['prefix'] . '_';
                    $btn = $prefix . "btn" . $b++;
                    $botones .= "<INPUT id='$btn' name='$btn' type=button value=\"" . $a_boton['txt'] . "\" onClick='" . $a_boton['click'] . "'>";
                }
                $botones .= "</td></tr>";
            }
        }
        if ($this->botones_grupo) {
            $b = 5;
        }

        $cab = 1;
        $aColsVisible = [];
        $num_col = 0;
        foreach ($a_cabeceras as $Cabecera) {
            $class = '';
            $width = '';
            $visible = true;
            if (is_array($Cabecera)) {
                $name = $Cabecera['name'];
                if (!empty($Cabecera['class'])) {
                    $class = "class=\"{$Cabecera['class']}\"";
                }
                if (!empty($Cabecera['width'])) {
                    $width = "width=\"{$Cabecera['width']}\"";
                }
                if (!empty($Cabecera['visible']) && strtolower($Cabecera['visible']) === 'no') {
                    $visible = false;
                }
            } else {
                $name = $Cabecera;
            }

            $aColsVisible[$num_col] = $visible;
            $num_col++;

            if ($visible) {
                if (!empty($name)) {
                    $cabecera .= "<th class=cabecera $width $class >" . trim($name) . "</th>\n";
                } else {
                    $cabecera .= "<th class=cabecera tipo='notext' $width $class ></th>\n";
                }
                $cab++;
            }
        }
        if (!empty($b) && $b !== 'x') {
            $cabecera = "<th class=cabecera tipo='notext' width='20' ></th>\n" . $cabecera;
            $cab++;
        }
        $cabecera .= "</tr>\n";
        $ahora = date("Hms");
        $f = 1;
        $tt .= "<!-- DEBUG HTML TABLE: id=$id_tabla, num_headers=" . count($a_cabeceras) . ", b=" . $b . " -->\n";

        if (isset($a_valores['select'])) {
            $a_valores_chk = $a_valores['select'];
            unset($a_valores['select']);
        } else {
            $a_valores_chk = [];
        }

        unset($a_valores['scroll_id']);
        foreach ($a_valores as $num_fila => $fila) {
            $clase = "imp";
            $f % 2 ? 0 : $clase = "par";
            $f++;
            $id_fila = $f . $ahora;
            if (!empty($fila['clase'])) {
                $clase .= " " . $fila['clase'];
            }
            $tbody .= "<tr id='$id_fila' class='$clase' onclick='fnjs_clic_fila(this, event)' data-json='" . htmlspecialchars(json_encode($fila), ENT_QUOTES, 'UTF-8') . "'>";

            if (!empty($b) && $b !== 'x') {
                if (isset($fila['sel'])) {
                    $valor = $fila['sel'];
                    if (is_array($valor)) {
                        $chk = !empty($valor['select']) ? $valor['select'] : "";
                        $id = $valor['id'];
                    } else {
                        $id = $valor;
                        $chk = "";
                    }
                    if (!empty($id)) {
                        if (!empty($a_valores_chk)) {
                            $chk = in_array($id, $a_valores_chk) ? 'checked' : '';
                        }
                        $tbody .= "<td tipo='sel' title='" . _("clic para seleccionar") . "'>";
                        $tbody .= "<input class='sel' type='checkbox' $chk  name='sel[]' id='a$id' value='$id'>";
                        $tbody .= "</td>";
                    } else {
                        $tbody .= "<td></td>";
                    }
                } else {
                    $tbody .= "<td></td>";
                }
            }

            $start_icol = 0;
            if (!isset($fila[0]) && isset($fila[1])) {
                $start_icol = 1;
            } elseif (isset($fila[0])) {
                $is_id = false;
                if (isset($fila['sel'])) {
                    $sel_id = is_array($fila['sel']) ? $fila['sel']['id'] : (string)$fila['sel'];
                    if (strpos($sel_id, (string)$fila[0]) !== false || preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}/i', (string)$fila[0])) {
                        $is_id = true;
                    }
                } elseif (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}/i', (string)$fila[0])) {
                    $is_id = true;
                }

                if ($is_id) {
                    $start_icol = 1;
                }
            }

            $icol_offset = 0;
            foreach ($a_cabeceras as $num_col => $Cabecera) {
                if ($aColsVisible[$num_col]) {
                    $field = (is_array($Cabecera) && !empty($Cabecera['field'])) ? $Cabecera['field'] : null;
                    $id = (is_array($Cabecera) && !empty($Cabecera['id'])) ? $Cabecera['id'] : null;

                    $valor = '';
                    if ($field && array_key_exists($field, $fila)) {
                        $valor = $fila[$field];
                    } elseif ($id && array_key_exists($id, $fila)) {
                        $valor = $fila[$id];
                    } else {
                        $target_idx = $start_icol + $icol_offset;
                        $valor = $fila[$target_idx] ?? '';
                        $icol_offset++;
                    }
                    if (is_array($valor)) {
                        $val = $valor['valor'];
                        $tbody .= "<td>";
                        if (!empty($valor['ira'])) {
                            $ira = $valor['ira'];
                            $tbody .= "<span class=\"link\" onclick=\"fnjs_update_div('#main','$ira')\" >$val</span>";
                        } elseif (!empty($valor['script'])) {
                            $ira = $valor['script'];
                            $tbody .= "<span class=\"link\" onclick='$ira' >$val</span>";
                        } else {
                            $tbody .= $val;
                        }

                        for ($idx = 2; $idx <= 3; $idx++) {
                            if (!empty($valor["ira$idx"])) {
                                $ira = $valor["ira$idx"];
                                $tbody .= " <span class=\"link\" onclick=\"fnjs_update_div('#main','$ira')\" >$val</span>";
                            }
                            if (!empty($valor["script$idx"])) {
                                $ira = $valor["script$idx"];
                                $tbody .= " <span class=\"link\" onclick='$ira' >$val</span>";
                            }
                        }

                        $tbody .= "</td>";

                    } else {
                        $valor = $valor ?? '';
                        if (preg_match("/^(\d)+[\/-](\d)+[\/-](\d\d)+$/", $valor)) {
                            [$d, $m, $y] = preg_split('/[:\/\.-]/', $valor);
                            $fecha_iso = date("Y-m-d", mktime(0, 0, 0, $m, $d, $y));
                            $tbody .= "<td class='fecha' fecha_iso='$fecha_iso'>$valor</td>";
                        } else {
                            $tbody .= "<td>$valor</td>";
                        }
                    }
                }
            }
            $tbody .= "</tr>\n";
        }

        if (!empty($b) && $b !== 'x') {
            $botones = "<tr class=botones><td colspan='$cab'>" . $botones;
        }
        $scroll_id = !empty($a_valores['scroll_id']) ? $a_valores['scroll_id'] : 0;
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

    public function text_first($a, $b): int
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
        return strcasecmp($a, $b);
    }

    public function getCsv($filename): void
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
        foreach ($a_valores as $num_fila => $fila) {
            $a_valores_simple = [];
            uksort($fila, [$this, 'text_first']);
            foreach ($fila as $col => $valor) {
                if ($col === "clase" || $col === "order" || $col === "select") {
                    continue;
                }
                if ($col === "sel") {
                    $id = is_array($valor) ? $valor['id'] : $valor;
                    if (!empty($id)) {
                        $a_valores_simple[] = $id;
                    }
                } elseif (is_array($valor)) {
                    $a_valores_simple[] = $valor['valor'];
                } else {
                    if (preg_match("/^(\d)+[\/-](\d)+[\/-](\d\d)+$/", $valor)) {
                        [$d, $m, $y] = preg_split('/[:\/\.-]/', $valor);
                        $fecha_iso = date("Y-m-d", mktime(0, 0, 0, $m, $d, $y));
                        $a_valores_simple[] = $fecha_iso;
                    } else {
                        $a_valores_simple[] = $valor;
                    }
                }
            }
            fputcsv($fp, $a_valores_simple, "\t", '"');
        }
        fclose($fp);
    }

    public function setGrupos(array $aGrupos): void
    {
        $this->aGrupos = $aGrupos;
    }

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

    public function setColVisible(array $aColVisible): void
    {
        $this->aColVisible = $aColVisible;
    }

    public function setDatos(array $aDatos): void
    {
        $this->aDatos = $aDatos;
    }

    public function setBotones(array $aBotones): void
    {
        $this->aBotones = $aBotones;
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
