<?php

namespace frontend\shared\web;

use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use function frontend\shared\helpers\is_true;

/**
 * Classe per gestionar llistes de dades tipus taula editables (SlickGrid).
 */
class TablaEditable
{
    /** @var list<string>|array<int|string, string> */
    private array $aGrupos = [];
    /** @var list<array<string, mixed>|string> */
    private array $aCabeceras = [];
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
    private string $supdateUrl = '';

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

    /** Literal JavaScript seguro (traducciones con apóstrofos, URLs, etc.). */
    private static function jsStringLiteral(string $value): string
    {
        $encoded = json_encode(
            $value,
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE
        );

        return is_string($encoded) ? $encoded : '""';
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
     * Llama al endpoint backend que devuelve las preferencias SlickGrid
     * (colVisible, colWidths, tamaños) para el usuario actual.
     *
     * @return array<int|string, mixed>|null
     */
    private function fetchPreferenciaTabla(string $id_tabla): ?array
    {
        $data = PostRequest::getDataFromUrl(
            '/src/usuarios/preferencia_tabla_get',
            ['id_tabla' => $id_tabla]
        );
        $slickgrid = $data['slickgrid'] ?? null;

        return is_array($slickgrid) ? $slickgrid : null;
    }

    public function mostrar_tabla(): string
    {
        return $this->mostrar_tabla_slickgrid();
    }

    /**
     * Muestra una tabla SlickGrid editable (contenido documentado en el original apps/web/TablaEditable.php).
     */
    public function mostrar_tabla_slickgrid(): string
    {
        $a_botones = $this->aBotones;
        $a_cabeceras = $this->aCabeceras;
        $a_valores = $this->aDatos;
        $id_tabla = $this->sid_tabla;
        $grid_width = '900';

        $sortcol = $this->ssortCol;
        $botones = "";
        $tt = "";
        $numBotones = 0;
        if ($a_valores === []) {
            return '<br>' . _("no hay ninguna fila");
        }
        foreach ($a_botones as $a_boton) {
            $btn = 'btn' . $numBotones++;
            $botones .= '<INPUT id="' . $btn . '" name="' . $btn . '" type=button value="' . self::scalarString($a_boton['txt'] ?? '') . '" onClick=\'' . self::scalarString($a_boton['click'] ?? '') . '\'>';
        }

        /** @var array<string, mixed>|string|null $aColsVisible */
        $aColsVisible = null;
        /** @var array<string, mixed> $aColsWidth */
        $aColsWidth = [];
        $bPanelVis = false;
        $grid_height = '0';
        $aPrefs = $this->fetchPreferenciaTabla($id_tabla);
        if (is_array($aPrefs)) {
            $colVisible = $aPrefs['colVisible'] ?? null;
            if (is_array($colVisible)) {
                $aColsVisible = $colVisible;
            } elseif (is_string($colVisible) && $colVisible !== '') {
                $aColsVisible = $colVisible;
            }
            $bPanelVis = self::scalarString($aPrefs['panelVis'] ?? '') === 'si';
            if (!empty($aPrefs['colWidths']) && is_array($aPrefs['colWidths'])) {
                $aColsWidth = $aPrefs['colWidths'];
            }
            $grid_width = self::scalarString($aPrefs['widthGrid'] ?? '900');
            $grid_height = self::scalarString($aPrefs['heightGrid'] ?? '0');
        }
        if ($this->aColVisible !== []) {
            $aColsVisible = $this->aColVisible;
        }
        if (!$this->bFiltro) {
            $bPanelVis = false;
        }

        $aCols = $this->getHeader($a_cabeceras, $aColsVisible, $aColsWidth);
        $sColumns = $aCols['cols'];
        $sColumnsVisible = $aCols['colsVivible'];
        $sColFilters = $aCols['colFilters'];

        $ahora = date("Hms");
        $f = 1;
        $aFilas = [];
        $scroll_id = !empty($a_valores['scroll_id']) ? self::scalarString($a_valores['scroll_id']) : '0';
        unset($a_valores['scroll_id']);
        $selectRaw = $a_valores['select'] ?? null;
        $a_valores_chk = is_array($selectRaw) ? $selectRaw : [];
        unset($a_valores['select']);
        foreach ($a_valores as $num_fila => $fila) {
            if (!is_array($fila)) {
                continue;
            }
            $f++;
            $id_fila = $f . $ahora;
            ksort($fila);
            $aFilas[$num_fila]["id"] = $id_fila;
            $aFilas[$num_fila]['editable'] = '';
            foreach ($fila as $col => $valor) {
                if ($col === "clase") {
                    $aFilas[$num_fila]["clase"] = self::scalarString($valor);
                    continue;
                }
                if ($col === "order" || $col === "select") {
                    continue;
                }
                if ($col === "sel") {
                    if ($numBotones === 0) {
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
                    if (is_array($valor)) {
                        $val = self::scalarString($valor['valor'] ?? '');
                        if (!empty($valor['editable']) && is_true($valor['editable'])) {
                            $aFilas[$num_fila]['editable'] .= ($aFilas[$num_fila]['editable'] !== '') ? ',' . $col : $col;
                        }
                        if (!empty($valor['editor'])) {
                            $aFilas[$num_fila]['editor'] = self::scalarString($valor['editor']);
                        }
                        if (!empty($valor['clase'])) {
                            $aFilas[$num_fila]['clase'] = self::scalarString($valor['clase']);
                        }
                        if (!empty($valor['ira'])) {
                            $aFilas[$num_fila]['ira'] = self::scalarString($valor['ira']);
                        }
                        if (!empty($valor['ira2'])) {
                            $aFilas[$num_fila]['ira2'] = self::scalarString($valor['ira2']);
                        }
                        if (!empty($valor['script'])) {
                            $aFilas[$num_fila]['script'] = self::scalarString($valor['script']);
                        }
                        if (!empty($valor['script2'])) {
                            $aFilas[$num_fila]['script2'] = self::scalarString($valor['script2']);
                        }
                        $aFilas[$num_fila][$col] = $val;
                    } else {
                        $aFilas[$num_fila][$col] = self::scalarString($valor);
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
            JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
        );
        if ($sData === false) {
            $sData = '[]';
        }

        if (($grid_height === '' || $grid_height === '0') && $f < 12) {
            $grid_height = (string) max(200, (4 + $f) * 25);
        } elseif ($grid_height === '' || $grid_height === '0') {
            $grid_height = '350';
        }

        $tt = "<input id=\"scroll_id\" name=\"scroll_id\" value=\"$scroll_id\" type=\"hidden\">";

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
				editable: true
                ,enableAutoResize: !!resizer
				,enableCellNavigation: true
				,enableAddRow: false
				,enableColumnReorder: true
				,asyncEditorLoading: false
				,autoEdit: false
				,topPanelHeight: 50
				,autoHeight: false
				,autosizeColsMode: Slick.GridAutosizeColsMode.LegacyForceFit
			};

			var sortcol = " . self::jsStringLiteral($sortcol) . ";
			var sortdir = 1;
			var searchString = \"\";
			var columnFilters_$id_tabla = $sColumns;
		
			function isCellEditable(row, cell) {
			 	item = dataView_$id_tabla.getItem(row);	
				tit = grid_$id_tabla.getColumns()[cell].field;
				if(item.editable.length) {
					var strVale = item.editable;
					arr = strVale.split(',');
					if ($.inArray( tit, arr ) !== -1 ) {
						return true;
					} else {
						return false;
					}
				} else {
					return false;
				}
			}

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
				$(\"#scroll_id\").val(row);	
			}
            
			function clickFormatter(row, cell, value, columnDef, dataContext) {
				if (ira=dataContext['ira']) {
					return \"<span class=link onclick=\\\"fnjs_update_div('#main','\"+ira+\"') \\\" >\"+value+\"</span>\";
				}
				if (ira=dataContext['script']) {
					return \"<span class=link onclick='grid_$id_tabla.setSelectedRows([\"+row+\"]);\"+ira+\";' >\"+value+\"</span>\";
				}
				return value;
			}
			function clickFormatter2(row, cell, value, columnDef, dataContext) {
				if (ira=dataContext['ira2']) {
					return \"<span class=link onclick=\\\"fnjs_update_div('#main','\"+ira+\"') \\\" >\"+value+\"</span>\";
				}
				if (ira=dataContext['script2']) {
					return \"<span class=link onclick=\"+ira+\" >\"+value+\"</span>\";
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
					grid_$id_tabla.setSelectedRows([row]);	
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
			function cssFormatter(row, cell, value, columnDef, dataContext) {
				if (isCellEditable(row, cell)) {
				    return \"<div style='background-color:white'>\"+value+\"</div>\";
                }
			}

			";
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
			
			function comparer(a,b) {
				var dateformat = /^\d{1,2}(\-|\/|\.)\d{1,2}(\-|\/|\.)\d{2,4}$/;
				var dateTimeFormat = /^\d{1,2}(\-|\/|\.)\d{1,2}(\-|\/|\.)\d{2,4} \d{2}:\d{2}:\d{2}$/;

				if ( dateTimeFormat.test(a[sortcol]) && dateTimeFormat.test(b[sortcol]) ) {
					var dateTime_a = a[sortcol].split(' ');
					var dateTime_b = b[sortcol].split(' ');
					var fecha_a = dateTime_a[0].split('/');
					var hora_a = dateTime_a[1].split(':');
					var fecha_b = dateTime_b[0].split('/');
					var hora_b = dateTime_b[1].split(':');
					var date_a = new Date(fecha_a[2], fecha_a[1], fecha_a[0], hora_a[0], hora_a[1], hora_a[2]);
					var date_b = new Date(fecha_b[2], fecha_b[1], fecha_b[0], hora_b[0], hora_b[1], hora_b[2]);
					var diff = date_a.getTime()-date_b.getTime();
					return (diff==0?diff:diff/Math.abs(diff));
				}
				if ( dateformat.test(a[sortcol]) && dateformat.test(b[sortcol]) ) {
					var tableau_a = a[sortcol].split('/');
					var tableau_b = b[sortcol].split('/');
					var date_a = new Date(tableau_a[2], tableau_a[1], tableau_a[0]);
					var date_b = new Date(tableau_b[2], tableau_b[1], tableau_b[0]);
					var diff = date_a.getTime()-date_b.getTime();
					return (diff==0?diff:diff/Math.abs(diff));
				} else {
					var x = a[sortcol], y = b[sortcol];
					if (isNaN(x) || isNaN(y)) {
						x=x.toUpperCase();
						y=y.toUpperCase();
						return (x == y ? 0 : (x > y ? 1 : -1));
					} else {
						int_a=parseInt(a[sortcol],10);
						int_b=parseInt(b[sortcol],10);
						return (int_a == int_b ? 0 : (int_a > int_b ? 1 : -1));
					}
				}
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
				
				grid_$id_tabla.onDblClick.subscribe(function (e,args) {
				  if (!isCellEditable(args.row, args.cell)) {
				    e.stopPropagation();
				  }
				});

				grid_$id_tabla.onBeforeEditCell.subscribe(function(e,args) {
				  if (!isCellEditable(args.row, args.cell)) {
					return false;
				  }
				});

				grid_$id_tabla.onSelectedRowsChanged.subscribe(function (e,args) {
					$.when($(\"input:checkbox\").prop('checked', false));
					$.when($(\".selected input:checkbox\").prop('checked', true));
				});
				
				grid_$id_tabla.onCellChange.subscribe(function (e, args) {
				  	if (isCellEditable(args.row, args.cell)) {
						updateItem_$id_tabla(args);
					}
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
					sortdir = args.sortAsc ? 1 : -1;
					sortcol = args.sortCol.field;

					dataView_$id_tabla.sort(comparer, args.sortAsc);
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


        if (!empty($this->getUpdateFunction())) {
            $tt .= "function updateItem_$id_tabla(args) {\n";
            $tt .= "     tit = grid_$id_tabla.getColumns()[args.cell].field;";
            $tt .= "     $(\"input[name='data']\").val(JSON.stringify(args.item));";
            $tt .= "     $(\"input[name='colName']\").val(JSON.stringify(tit));";
            $tt .= $this->getUpdateFunction();
            $tt .= "}";
        }

        if ($scroll_id !== '' && $scroll_id !== '0') {
            $tt .= " grid_$id_tabla.scrollRowToTop($scroll_id);";
        }

        if ($bPanelVis) $tt .= "toggleFilterRow_$id_tabla();";

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

        $oHash = new HashFront();
        $oHash->setCamposNo('data!colName');
        $a_camposHidden = ['que' => 'update'];
        $oHash->setArraycamposHidden($a_camposHidden);

        $ta .= "<form id=\"form_update\" action=\"?\" method=\"POST\">";
        $ta .= $oHash->getCamposHtml();
        $ta .= "
		  <input type=\"hidden\" name=\"data\" value=\"\">
		  <input type=\"hidden\" name=\"colName\" value=\"\">
		</form>
		";

        return $ta . $tt;
    }

    /**
     * @param list<array<string, mixed>|string> $aHeader
     * @param array<string, mixed>|string|null $aColsVisible
     * @param array<string, mixed> $aColsWidth
     * @return array{cols: string, colsVivible: string, colFilters: string, header_num: int}
     */
    private function getHeader(array $aHeader, array|string|null $aColsVisible, array $aColsWidth): array
    {
        $header_num = 1;
        $sColumns = '';
        $sColumnsVisible = '';
        $sColFilters = '';
        foreach ($aHeader as $Cabecera) {
            $visible = true;
            if (is_array($Cabecera)) {
                $name = self::scalarString($Cabecera['name'] ?? '');
                $name_idx = str_replace(' ', '', $name);
                $id = self::cabeceraFieldKey($Cabecera, 'id') ?? $name_idx;
                $field = self::cabeceraFieldKey($Cabecera, 'field') ?? '';
                $title = self::scalarString($Cabecera['title'] ?? $name);
                $toolTip = ', toolTip: ' . self::jsStringLiteral($title);
                $class = !empty($Cabecera['class']) ? ', cssClass: ' . self::jsStringLiteral(self::scalarString($Cabecera['class'])) : '';
                $sortable = !empty($Cabecera['sortable']) ? self::scalarString($Cabecera['sortable']) : 'true';
                $width = !empty($Cabecera['width']) ? self::scalarString($Cabecera['width']) : '';
                $formatter = !empty($Cabecera['formatter']) ? self::scalarString($Cabecera['formatter']) : '';
                $editor = !empty($Cabecera['editor']) ? self::scalarString($Cabecera['editor']) : '';
                $vis = self::scalarString($Cabecera['visible'] ?? '');
                if ($vis === 'No' || $vis === 'no') {
                    $visible = false;
                }

                $sDefCol = '{id: ' . self::jsStringLiteral($id) . ', name: ' . self::jsStringLiteral($name) . ', sortable: ' . $sortable . $class . $toolTip;

                if ($field !== '') {
                    $sDefCol .= ', field: ' . self::jsStringLiteral($field) . ' ';
                }

                if (isset($aColsWidth[$name_idx])) {
                    $sDefCol .= ', width: ' . self::scalarString($aColsWidth[$name_idx]);
                } elseif ($width !== '') {
                    $sDefCol .= ', width: ' . $width;
                }

                if ($formatter !== '') {
                    $sDefCol .= ', formatter: ' . $formatter;
                }
                if ($editor !== '') {
                    $sDefCol .= ', editor: ' . $editor;
                }

                if (!empty($Cabecera['children'])) {
                    $sDefCol .= ', colspan: 2';
                }
                $sDefCol .= '}';

            } else {
                $name = self::scalarString($Cabecera);
                $name_idx = str_replace(' ', '', $name);
                $toolTip = ', toolTip: ' . self::jsStringLiteral($name);
                $sDefCol = '{id: ' . self::jsStringLiteral($name_idx) . ', name: ' . self::jsStringLiteral($name) . ', field: ' . self::jsStringLiteral($name_idx) . ', sortable: true' . $toolTip;
                if (isset($aColsWidth[$name_idx])) {
                    $sDefCol .= ', width: ' . self::scalarString($aColsWidth[$name_idx]);
                }
                $sDefCol .= '}';
            }
            if ((is_array($aColsVisible) && !empty($aColsVisible[$name_idx]) && is_true($aColsVisible[$name_idx]))
                || !is_array($aColsVisible)) {
                if (!$visible) {
                    continue;
                }
                $sColumnsVisible .= $sColumnsVisible === '' ? $sDefCol : ',' . $sDefCol;
            }

            $sColumns .= $sColumns === '' ? $sDefCol : ',' . $sDefCol;
            $sColFilters .= $sColFilters === '' ? self::jsStringLiteral($name_idx) : ',' . self::jsStringLiteral($name_idx);
        }
        $sColumns = '[' . $sColumns . ']';
        $sColumnsVisible = '[' . $sColumnsVisible . ']';
        $sColFilters = '[' . $sColFilters . ']';

        return ['cols' => $sColumns, 'colsVivible' => $sColumnsVisible, 'colFilters' => $sColFilters, 'header_num' => $header_num];
    }

    /** @return list<string>|array<int|string, string> */
    public function getGrupos(): array
    {
        return $this->aGrupos;
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

    public function getUpdateUrl(): string
    {
        return $this->supdateUrl;
    }

    public function setUpdateUrl(string $supdateUrl): void
    {
        $this->supdateUrl = $supdateUrl;
    }

    public function getUpdateFunction(): string
    {
        $fnjs = '';
        $url = $this->getUpdateUrl();
        if (!empty($url)) {
            $txtRespuesta = _('respuesta');
            $txtError = _('hay un error, no se ha guardado');
            $urlJs = self::jsStringLiteral($url);
            $txtRespuestaJs = self::jsStringLiteral($txtRespuesta);
            $txtErrorJs = self::jsStringLiteral($txtError);
            $fnjs = "
				var url={$urlJs};
				\$('#form_update').one('submit', function() {
					\$.ajax({
						url: url,
						type: 'post',
						data: \$(this).serialize(),
						dataType: 'json'
					})
					.done(function (json) {
						if (!json || json.success !== true) {
							var msg = (json && json.mensaje) ? json.mensaje : {$txtErrorJs};
							alert({$txtRespuestaJs} + ': ' + msg);
						}
					})
					.fail(function (xhr) {
						alert({$txtRespuestaJs} + ': ' + (xhr.responseText || {$txtErrorJs}));
					});
					return false;
				});
				\$('#form_update').trigger(\"submit\");
				";
        }
        return $fnjs;
    }
}
