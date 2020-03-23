<?php
namespace web;
use usuarios\model\entity as usuarios;
use core;
/**
 * TablaEditable
 *
 * Classe per gestionar llistes de dades tipus taula.
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 18/10/2010
 */

class TablaEditable {
	/* ATRIBUTS ----------------------------------------------------------------- */
	/**
	 * sNombre del Lista
	 *
	 * @var string
	 */
	 protected $sNombre;
	/**
	 * ikey del Lista
	 *
	 * @var integer ?
	 */
	 protected $ikey;

	/**
	 * aGrupos de la Lista
	 *
	 * @var array ( id => titulo)
	 */
	 protected $aGrupos;

	/**
	 * aCabeceras de la Lista
	 *
	 * @var array
	 */
	 protected $aCabeceras;
	/**
	 * ssortcol de la Lista. Columna por la que se ordena la tabla inicialmente.
	 *
	 * @var string
	 */
	 protected $ssortCol;
	/**
	 * aColVisible de la Lista. columnas visibles inicialmente.
	 *
	 * @var array
	 */
	 protected $aColVisible = array();
	/**
	 * aDatos de la Lista
	 *
	 * @var array lista de arrays (id el del titulo) cada sub-array es la fila.
	 */
	 protected $aDatos;
	/**
	 * aBotones de la Lista
	 *
	 * @var array
	 */
	 protected $aBotones;
	/**
	 * sid_tabla de la Lista
	 *
	 * @var array
	 */
	protected $sid_tabla = 'uno';
	/**
	 * bFiltro de la Lista
	 *
	 * @var boolean
	 */
	protected $bFiltro = true;
	/**
	 * bColVis de la Lista
	 *
	 * @var boolean
	 */
	protected $bColVis = true;
	/**
	 * bRecordar de la Lista
	 *
	 * @var boolean
	 */
	protected $bRecordar = true;
	/**
	 * supdateUrl de la Lista
	 *
	 * @var array
	 */
	protected $supdateUrl = '';

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 * @return TablaEditable
	 *
	 */
	function __construct() {
		// constructor buit
	}

	/**
	 * Muestra una tabla ordenable, con  botones en la cabecera y check box en cada lina.
	 *
	 *@return string Html
	 *
	 */
	function mostrar_tabla() {
		return $this->mostrar_tabla_slickgrid();
		/*
		$sPrefs = '';
		$id_usuario= core\ConfigGlobal::mi_id_usuario();
		$tipo = 'tabla_presentacion';
		$oPref = new usuarios\Preferencia(array('id_usuario'=>$id_usuario,'tipo'=>$tipo));
		$sPrefs=$oPref->getPreferencia();
		if ($sPrefs == 'html') {
			return $this->mostrar_tabla_html();
		} else {
			return $this->mostrar_tabla_slickgrid();
		}
		*/
	}
	
	/**
	 * Muestra una tabla ordenable, con  botones en la cabecera y check box en cada lina.
	 **
	 * $a_cabeceras:  array( 
   	 *		[col] = array('name'=>_("inicio"),'width'=>40,'class'=>'fecha', 'formatter'=>'clickFormatter')
	 *				'name'=> texto de la cabecera de la columna
	 * 				'width'=> ancho de la columna
	 * 				'class'=> se añade al atributo class
	 *				'formatter'=> ['clickFormatter'|'clickFormatter2'] post-formateo a aplicar en la columna. Para el caso de 'ira', 'script' y  'ira2', 'script2' respectivamente 
	 * 
	 * $a_valores:  array(
	 * 	[fila]['clase'] = valor => añade añade 'valor' en el atributo class de la fila
	 * 	[fila]['sel'] = valor => crea la colummna sel con un checkbox con id='#'.addslashes(valor)
	 * 	[fila]['select'] = array(valor)	=> marca como checked los checkbox de la columna 'sel' con id = valor
	 * 	[fila]['scroll_id'] = valor	=> ejecuta: " grid_$id_tabla.scrollRowToTop($scroll_id);" Que desplaza las filas hasta la linea correspondiente.
	 * 	[fila][col] = txt
	 * 	[fila][col] = array( 'ira'=>$pagina, 'valor'=>$txt) 
	 * 				=> crea un 'link' que ejecuta "fnjs_update_div('#main','pagina'):
	 * 			  	return \"<span class=link onclick=\\\"fnjs_update_div('#main','\"+ira+\"') \\\" >\"+value+\"</span>\";
	 * 	[fila][col] = array( 'script'=>$script, 'valor'=>$txt);
	 * 				=> crea un 'link' que ejecuta  al funcion de $cript:
					return \"<span class=link onclick='this.closest(\\\".slick-cell\\\").click();\"+ira+\";' >\"+value+\"</span>\";
	 * 	[fila][col] = array( 'span'=>3, 'valor'=> $txt) => de momento no hace nada. Sirve para la funcion mostrar_tabla_html
	 * 
	 *@return string Html Grid
	 * 	
	 * Slick.Editors.Text
    	Slick.Editors.LongText
    	Slick.Editors.PercentComplete
    	Slick.Editors.Date
	 * 
	 *  "Editors": {
        "Text": TextEditor,
        "Integer": IntegerEditor,
        "Date": DateEditor,
        "YesNoSelect": YesNoSelectEditor,
        "Checkbox": CheckboxEditor,
        "PercentComplete": PercentCompleteEditor,
        "LongText": LongTextEditor
	 	}
	 * 
	 */
	function mostrar_tabla_slickgrid() {
		$a_botones = $this->aBotones;
		$a_cabeceras = $this->aCabeceras;
		$a_valores = $this->aDatos;
		$id_tabla = $this->sid_tabla;
		$grid_width = '900';
		$heigth_width = '400';

		$sortcol=$this->ssortCol;
		$botones="";
		$cabecera="";
		$tt="";
		$clase="";
		$chk="";
		$b=0;
		if (empty($a_valores)) {
			return	'<br \>'._("no hay ninguna fila");
		}
		if (!empty($a_botones)) {
			if ($a_botones=="ninguno") {
				$b="x";
			} else {
				foreach ($a_botones as $a_boton) {
					$btn="btn".$b++;
					$botones .= "<INPUT id='$btn' name='$btn' type=button value=\"".$a_boton['txt']."\" onClick='".$a_boton['click']."'>";
				}
			}
		}
		
		$id_usuario= core\ConfigGlobal::mi_id_usuario();
		$idioma= core\ConfigGlobal::mi_Idioma();
		$tipo='slickGrid_'.$id_tabla.'_'.$idioma;
		$aUser = array( 0=>$id_usuario,1=>44); // 44 es el id_usuario para default.
		$aColsVisible = '';
		$bPanelVis = false;
		for($i=0;$i<count($aUser);$i++) {
			$user = $aUser[$i];
			$oPref = new usuarios\Preferencia(array('id_usuario'=>$user,'tipo'=>$tipo));

			if ($sPrefs=$oPref->getPreferencia()) {;
				$aPrefs = json_decode($sPrefs, true);
				if (!empty($aPrefs['colVisible'])) {
					$aColsVisible = empty($aPrefs['colVisible'])? '*' : $aPrefs['colVisible'];
					//$aColsVisible = explode(',',$aPrefs['colVisible']);
				}
				$bPanelVis = ($aPrefs['panelVis'] == "si")? true: false;
				if (!empty($aPrefs['colWidths'])) {
					$aColsWidth = $aPrefs['colWidths'];
				}
				// Anchura del grid
				$grid_width = (!empty($aPrefs['widthGrid']))? $aPrefs['widthGrid'] : '900';
				// Altura del grid
				$grid_height = (!empty($aPrefs['heightGrid']))? $aPrefs['heightGrid'] : '500';
				
				break; // sale del bucle.
			} else { // buscar las opciones por defecto
				continue;
			}
		}

		if ($b != 0 && $b != 'x') {
			$width = isset($aColsWidth['sel'])? $aColsWidth['sel'] : 30;
			$sColumns.= "{id: \"sel\", name: \"sel\", field: \"sel\", width:$width, sortable: false, formatter: checkboxSelectionFormatter}";
			if (!is_array($aColsVisible) || $aColsVisible['sel']=="true") {
				$sColumnsVisible .= "{id: \"sel\", name: \"sel\", field: \"sel\", width:$width, sortable: false, formatter: checkboxSelectionFormatter},";
			}
		}

		$aCols = $this->getHeader($a_cabeceras);
		$sColumns = $aCols['cols'];
		$sColumnsVisible = $aCols['colsVivible'];
		$sColFilters = $aCols['colFilters'];
		$header_num =  $aCols['header_num'];
		
		// Para generar un id único
		$ahora=date("Hms");
		$f=1;
		$aFilas = array();
		$scroll_id = !empty($a_valores['scroll_id'])? $a_valores['scroll_id'] : 0;
		unset($a_valores['scroll_id']);
		if (isset($a_valores['select'])) {
			$a_valores_chk = $a_valores['select'];
		} else {
			$a_valores_chk = array();
		}
		foreach($a_valores as $num_fila=>$fila) {
			$f++;
			$id_fila=$f.$ahora;
			ksort($fila);
			$aFilas[$num_fila]["id"] = $id_fila;
			$aFilas[$num_fila]['editable'] = ''; 
			foreach ($fila as $col=>$valor) {
				if ($col=="clase") {
					$id=$valor;
					$aFilas[$num_fila]["clase"] = addslashes($id);
					continue;
				}
				if ($col=="order") { continue; }
                if ($col=="select") { continue; }
				if ($col=="sel") {
					if (empty($b)) continue; // si no hay botones (por permisos...) no tiene sentido el checkbox
					//$col="";
					if(is_array($valor)) {
						if (!empty($valor['select'])) { $chk=$valor['select']; } else { $chk=""; }
						$id=$valor['id'];
					} else {
						$id=$valor;
					}
					if (!empty($id)) {
						if ( in_array($id, $a_valores_chk)) { $chk ='checked'; } else { $chk = ''; }
						$aFilas[$num_fila]["sel"] = $chk.'#'.addslashes($id);
					} else { // no hay que dibujar el checkbox, pero si la columna
						$aFilas[$num_fila]["sel"] = '';
					}
				} else {
					if(is_array($valor)) {
						$val=$valor['valor'];
						if ( !empty($valor['editable']) ) {
							if ($valor['editable'] =='true') {
								$aFilas[$num_fila]['editable'] .= (!empty($aFilas[$num_fila]['editable']))? ",".$col : $col;
							}
						}
						if ( !empty($valor['editor']) ) {
							$ira=$valor['editor'];
							$aFilas[$num_fila]['editor'] = $ira;
						}
						if ( !empty($valor['clase']) ) {
							$ira=$valor['clase'];
							$aFilas[$num_fila]['clase'] = $ira;
						}
						if ( !empty($valor['ira']) ) {
							$ira=$valor['ira'];
							$aFilas[$num_fila]['ira'] = $ira;
						}
						if ( !empty($valor['ira2']) ) {
							$ira=$valor['ira2'];
							$aFilas[$num_fila]['ira2'] = $ira;
						}
						if (!empty($valor['script']) ) {
							$ira=$valor['script'];
							$aFilas[$num_fila]['script'] = addslashes($ira);
						}
						if (!empty($valor['script2']) ) {
							$ira=$valor['script2'];
							$aFilas[$num_fila]['script2'] = addslashes($ira);
						}
						if (!empty($valor['span'])) {
							$span="$val";
						}
						$aFilas[$num_fila][$col] = addslashes($val);
					} else {
						$aFilas[$num_fila][$col] = addslashes($valor);
					}
				}
			}
		}

		$f = 0;
		$sData = '[';
		foreach($aFilas as $num_fila=>$fila) {
			$f++;
			if ($f>1) $sData .= ',';
			$c=0;
			$sEdit = '';
			$sData .= '{';
			foreach($fila as $camp=>$valor) {
				$c++;
				if ($c>1) $sData .= ',';
					$val=$valor;
					// Sólo elimino los saltos de lineas. las comillas las pone bien.
					$remove = array("\r\n", "\n", "\r");
					$val = str_replace($remove, ' ', $val);
					$sData .= "\"$camp\": \"$val\""; 
			}
			$sData .= '}';
		}
		$sData .= ']';

		
		// calculo la altura de la tabla
		if (empty($grid_height) && $f < 12) {
			$grid_height = (4+$f)*25; // +4 (cabecera y última linea en blanco). 25 = rowheight
			// mínimo, porque sino al deplegar el cuadro de búsqueda tapa tota la información
			$grid_height = ($grid_height < 200)? 200 : $grid_height;
		} else {
			$grid_height = empty($grid_height)? 350 : $grid_height;
		}

		$tt = "<input id=\"scroll_id\" name=\"scroll_id\" value=\"$scroll_id\" type=\"hidden\">";
			
		$tt .= "
			<script>
			var dataView_$id_tabla;
			var grid_$id_tabla;
			var columns_$id_tabla = $sColumnsVisible;
			var columnsAll_$id_tabla = $sColumns;
			var data_$id_tabla = $sData;

			var options = {
				editable: true
				,enableAddRow: false
				,enableCellNavigation: true
				,asyncEditorLoading: false
				,autoEdit: false
				,enableColumnReorder: true
				,forceFitColumns: true
				,topPanelHeight: 50
				,autoHeight: false
			};

			var sortcol = \"".$sortcol."\";
			var sortdir = 1;
			var searchString = \"\";
			var columnFilters_$id_tabla = $sColumns;
		
			function isCellEditable(row, cell) {
			 	item = dataView_$id_tabla.getItem(row);	
				//console.log(item);
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

			function metadata(old_metadata_provider) {
			  return function(row) {
				var item = this.getItem(row);
				var ret  = (old_metadata_provider(row) || {});
				//console.log(item);
				if (item) {
				  ret.cssClasses = (ret.cssClasses || '');
				  if (item.clase) {
					ret.cssClasses += item.clase;
				  }
				}
				return ret;
			  }
			}
			
			function add_scroll_id(row) {
				$(\"#scroll_id\").val(row);	
				//console.log(row);
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
					//return \"<span class=link onclick=fnjs_update_div('#main',\\\"\"+ira+\"\\\") >\"+value+\"</span>\";
					return \"<span class=link onclick=\\\"fnjs_update_div('#main','\"+ira+\"') \\\" >\"+value+\"</span>\";
				}
				if (ira=dataContext['script2']) {
					//return \"<span class=link onclick='\"+dataContext['script']+\"' >\"+value+\"</span>\";
					return \"<span class=link onclick=\"+ira+\" >\"+value+\"</span>\";
				}
				return value;
			}
			function checkboxSelectionFormatter(row, cell, value, columnDef, dataContext) {
				if (value == null || value === \"\") {
				  return \"\";
				} else {
				  // formato: checked#id#nom_activ [#otro#otro..n]
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
			
			// Define search filter
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
				.mouseover(function (e) {
				  $(e.target).addClass(\"ui-state-hover\")
				})
				.mouseout(function (e) {
				  $(e.target).removeClass(\"ui-state-hover\")
				});


			$(function () {
				dataView_$id_tabla = new Slick.Data.DataView();
				grid_$id_tabla = new Slick.Grid(\"#grid_$id_tabla\", dataView_$id_tabla, columns_$id_tabla, options);
				grid_$id_tabla.setSelectionModel(new Slick.RowSelectionModel());
				grid_$id_tabla.registerPlugin(new Slick.AutoTooltips());
                grid_$id_tabla.registerPlugin(new Slick.AutoColumnSize());

				var pager = new Slick.Controls.Pager(dataView_$id_tabla, grid_$id_tabla, $(\"#pager\"));
				var columnpicker = new Slick.Controls.ColumnPicker(columnsAll_$id_tabla, grid_$id_tabla, options);

				// move the filter panel defined in a hidden div into grid top panel
				$(\"#inlineFilterPanel_".$id_tabla."\")
				  .appendTo(grid_$id_tabla.getTopPanel())
				  .show();
				  
				dataView_$id_tabla.getItemMetadata = metadata(dataView_$id_tabla.getItemMetadata);
					
				grid_$id_tabla.onClick.subscribe(function (e,args) {
					add_scroll_id(args.row);
					grid_$id_tabla.setSelectedRows([args.row]);
					//e.stopPropagation();
				});
				
				grid_$id_tabla.onDblClick.subscribe(function (e,args) {
				  if (!isCellEditable(args.row, args.cell)) {
					//return false;
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
					//e.stopPropagation();
				});
				
				grid_$id_tabla.onCellChange.subscribe(function (e, args) {
					//Updated code as per comment.
					//console.log(args); 
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
				  // select all rows on ctrl-a
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
				// wire up model events to drive the grid
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

				// wire up the search textbox to apply the filter to the model
				$(\"#txtSearch_".$id_tabla."\").keyup(function (e) {
					Slick.GlobalEditorLock.cancelCurrentEdit();
					// clear on Esc
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
			// initialize the model after all the events have been hooked up
			dataView_$id_tabla.beginUpdate();
			dataView_$id_tabla.setItems(data_$id_tabla);
			dataView_$id_tabla.setFilterArgs({
				searchString: searchString
			});
			dataView_$id_tabla.setFilter(myFilter_$id_tabla);
			dataView_$id_tabla.endUpdate();
			$(\"#grid_$id_tabla\").resizable();
		";


		if (!empty($this->getUpdateFunction())) {
			$tt .= "function updateItem_$id_tabla(args) {\n";
			$tt .= "     tit = grid_$id_tabla.getColumns()[args.cell].field;";
			$tt .= "     $(\"input[name='data']\").val(JSON.stringify(args.item));";
			$tt .= "     $(\"input[name='colName']\").val(JSON.stringify(tit));";
			$tt .= $this->getUpdateFunction();
			$tt .= "}";
		}
		
		if (isset($scroll_id)) {
			$tt .= " grid_$id_tabla.scrollRowToTop($scroll_id);";
		}

		if ($bPanelVis) $tt .= "toggleFilterRow_$id_tabla();";
		
		$tt .= "
			var container = $(grid_$id_tabla.getContainerNode());
			var h_header =  $('.grid-header').height();
			var vph = $grid_height - h_header;
			container.height(vph); 
			grid_$id_tabla.resizeCanvas();
		  })
		</script>
		";

		//$tt.="<div id=\"GridContainer_".$id_tabla."\"  style=\"width:{$grid_width}px; height:{$grid_height}px;\" >
		//OJO. si heigth no es auto, al hacer resize desde html, los textos de debajo de la grid no se mueven.
		$tt.="<div id=\"GridContainer_".$id_tabla."\"  style=\"width:{$grid_width}px; height:auto;\" >
		<div class=\"grid-header\">
		  <span style=\"width:90%; display: inline-block;\">$botones</span>
		  <span style=\"float:right\" class=\"ui-icon ui-icon-disk\" title=\""._("guardar selección de columnas")."\"
				onclick=\"fnjs_def_tabla('".$id_tabla."')\"></span>
		  <span style=\"float:right\" class=\"ui-icon ui-icon-search\" title=\""._("ver/ocultar panel de búsqueda")."\"
				onclick=\"toggleFilterRow_$id_tabla()\"></span>
		</div>
		<div id=\"grid_$id_tabla\"  style=\"width:{$grid_width}px;\"></div>
		";
		$tt.="</div>";
		
		$tt.="
		<div id=\"inlineFilterPanel_".$id_tabla."\" style=\"display:none;background:#dddddd;padding:3px;color:black;\">
		  "._("Buscar en todas las columnas")." <input type=\"text\" id=\"txtSearch_".$id_tabla."\">
		</div>
		";
		
		$oHash = new Hash();
		$oHash->setCamposNo('data!colName');
		$a_camposHidden = array( 'que' => 'update');
		$oHash->setArraycamposHidden($a_camposHidden);
		
		$tt.="<form id=\"form_update\" action=\"?\" method=\"POST\">";
		$tt.= $oHash->getCamposHtml();
		$tt.="
		  <input type=\"hidden\" name=\"data\" value=\"\">
		  <input type=\"hidden\" name=\"colName\" value=\"\">
		</form>
		";
		return $tt;
	}

	public function getHeader($aHeader){
		global $aColsVisible;
		$header_num = 1;
		$sColumns = '';
		$sColumnsVisible = '';
		$sColFilters = '';
		foreach($aHeader as $Cabecera) {
			$visible = TRUE;
			if (is_array($Cabecera)) {
				$name = $Cabecera['name']; // esta tiene que existir siempre
				$name_idx = str_replace(' ','',$name); // quito posibles espacios en el indice
				$id = !empty($Cabecera['id'])? $Cabecera['id'] : str_replace(' ','',$name); // quito posibles espacios en el indice
				//$field = !empty($Cabecera['field'])? $Cabecera['field'] :  str_replace(' ','',$name); // quito posibles espacios en el indice
				$field = !empty($Cabecera['field'])? $Cabecera['field'] :  '';
				$toolTip = !empty($Cabecera['title'])? ", toolTip: \"${Cabecera['title']}\"" : ", toolTip: \"${Cabecera['name']}\"";
				$class = !empty($Cabecera['class'])? ", cssClass: \"${Cabecera['class']}\"" : '';
				$sortable = !empty($Cabecera['sortable'])? $Cabecera['sortable'] : 'true';
				$width = !empty($Cabecera['width'])? $Cabecera['width'] : '';
				$formatter = !empty($Cabecera['formatter'])? $Cabecera['formatter'] : '';
				$editor = !empty($Cabecera['editor'])? $Cabecera['editor'] : '';
				if (!empty($Cabecera['visible'])) {
					if ($Cabecera['visible'] == 'No' || $Cabecera['visible'] == 'no' ) { $visible = FALSE; }
				}
				
				$sDefCol = "{id: \"$id\", name: \"$name\", sortable: $sortable".$class.$toolTip;
				
				if (!empty($field)) $sDefCol .= ", field: \"$field\" ";

				if (isset($aColsWidth[$name_idx])) {
					$sDefCol .= ", width: ".$aColsWidth[$name_idx];
				} else {
					if (!empty($width)) $sDefCol .= ", width: $width";
				}

				if (!empty($formatter)) $sDefCol .= ", formatter: $formatter";
				if (!empty($editor)) $sDefCol .= ", editor: $editor";

				if (!empty($Cabecera['children'])) {
                    $sDefCol .= ", colspan: 2";
				    /*
					$aColss = $this->getHeader($Cabecera['children']);
					$sColss = $aColss['cols'];
					$sDefCol .= ", children: $sColss";
					// si tiene sub-titulos, amplio la altura de la tabla.
					$header_num = 2;
					*/
				}
				$sDefCol .= "}";
					
			} else {
				$name = $Cabecera;
				$name_idx = str_replace(' ','',$Cabecera); // quito posibles espacios en el indice
				$toolTip = ", toolTip: \"$name\"";
				$sDefCol = "{id: \"$name_idx\", name: \"$name\", field: \"$name_idx\", sortable: true".$toolTip;
				if (isset($aColsWidth[$name_idx])) {
					$sDefCol .= ", width: ".$aColsWidth[$name_idx];
				}
				$sDefCol .= "}"; 
			}
			if ((is_array($aColsVisible) && !empty($aColsVisible[$name_idx]) && ($aColsVisible[$name_idx]=="true")) || !is_array($aColsVisible)) {
				if (!$visible) { continue; }
				$sColumnsVisible .= empty($sColumnsVisible)? $sDefCol : ','.$sDefCol;
			}
			
			$sColumns .= empty($sColumns)? $sDefCol : ','.$sDefCol;
			$sColFilters .= empty($sColFilters)? "\"$name_idx\"" : ",\"$name_idx\"";
			
		}
		$sColumns = '['.$sColumns.']';
		$sColumnsVisible = '['.$sColumnsVisible.']';
		$sColFilters = '['.$sColFilters.']';

		return array('cols'=>$sColumns,'colsVivible'=>$sColumnsVisible,'colFilters'=>$sColFilters,'header_num'=>$header_num);
	}
	/* METODES GET i SET ----------------------------------------------------------*/

	public function setGrupos($aGrupos) {
		 $this->aGrupos = $aGrupos;
	}
	public function setCabeceras($aCabeceras) {
		 $this->aCabeceras = $aCabeceras;
	}
	public function setSortCol($ssortcol) {
		 $this->ssortCol = str_replace(' ','',$ssortcol);
	}
	public function setColVisible($aColVisible) {
		 $this->aColVisible = $aColVisible;
	}
	public function setDatos($aDatos) {
		 $this->aDatos = $aDatos;
	}
	public function setBotones($aBotones) {
		 $this->aBotones = $aBotones;
	}
	public function setId_tabla($sid_tabla) {
		 $this->sid_tabla = $sid_tabla;
	}
	public function setFiltro($bFiltro) {
		 $this->bFiltro = $bFiltro;
	}
	public function setColVis($bColVis) {
		 $this->bColVis = $bColVis;
	}
	public function setRecordar($bRecordar) {
		 $this->bRecordar = $bRecordar;
	}
	public function getUpdateUrl() {
		 return $this->supdateUrl;
	}
	public function setUpdateUrl($supdateUrl) {
		 $this->supdateUrl = $supdateUrl;
	}
	
	public function getUpdateFunction() {
		$fnjs = '';
		$url = $this->getUpdateUrl();
		if (!empty($url)) {
			$fnjs ="
				var url='$url';
				$('#form_update').one('submit', function() {
					$.ajax({
						url: url,
						type: 'post',
						data: $(this).serialize()
					})
					.done(function (rta_txt) {
						if (rta_txt != '' && rta_txt != '\\n') {
							alert ('<?= _(\"respuesta\") ?>: '+rta_txt);
						}
					});
					return false;
				});
				$('#form_update').submit();
				";
		}
		return $fnjs;
	}
}
?>
